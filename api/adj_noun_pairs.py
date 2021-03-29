from flask import Blueprint, jsonify, request
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('adj_noun_pairs', __name__, url_prefix='/adj_noun_pairs')

@bp.route('/<int:store_id>')
def adj_noun_pairs(store_id):
    df = None
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return __response_invalid("The store identifier was not found")
        try:
            all_pairs = dao.getAdjNounPairs(store_id)
            df = __getRanking(all_pairs)
        except Exception as e:
            return jsonify(error=msg), 400
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200


@bp.route('/', methods=["POST"])
def get_adj_noun_pair():
    df = None
    request_data = request.get_json()
    ids = request_data["data"]
    with DataAccess() as dao:
        try:
            all_pairs = dao.getAdjNounPairsByIds(ids)
            df = __getRanking(all_pairs)
        except Exception as e:
            return jsonify(error=msg), 400
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200


def __getRanking(all_pairs):
    num_of_noun = 10
    num_of_adj_each = 3
    top_noun = all_pairs.groupby(['noun']).size().reset_index(
        name='count').sort_values(['count'], ascending=False).head(num_of_noun)
    filtered_nouns = all_pairs[all_pairs.noun.isin(
        top_noun.noun.to_list())]
    noun_top_adj_ranking = filtered_nouns.groupby(['noun', 'adj']).size(
    ).reset_index(name='count').sort_values(['count'], ascending=False)
    filtered_adj = filtered_nouns[filtered_nouns.adj.isin(
        noun_top_adj_ranking.adj.to_list())]
    pairs = filtered_adj.groupby(['noun', 'adj'])[
        'review_id'].apply(','.join).reset_index()
    pairs['review_id'] = pairs.review_id.apply(lambda x: x.split(","))
    pairs['count'] = pairs.review_id.apply(lambda x: len(x))
    df = pairs.groupby(['noun']).apply(lambda x: x.nlargest(num_of_adj_each, ['count'], keep='first')).reset_index(
        drop=True).sort_values(['noun', 'count'], ascending=False).reset_index(drop=True)
    return df