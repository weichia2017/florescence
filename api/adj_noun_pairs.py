from flask import Blueprint, jsonify, request
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('adj_noun_pairs', __name__, url_prefix='/adj_noun_pairs')


@bp.route('/<int:store_id>')
def redirect_to_pairsByStore(store_id):
    return pairsByStores(store_id)


@bp.route('/store/<int:store_id>')
def pairsByStores(store_id):
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return jsonify(error="The store identifier was not found"), 400
        try:
            all_pairs = dao.getAdjNounPairsByStore(store_id)
            df = __getRanking(all_pairs)
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500


@bp.route('/road/<int:road_id>')
def pairsByRoad(road_id):
    df = None
    with DataAccess() as dao:
        if road_id not in dao.getRoads().index.to_list():
            return jsonify(error="The road identifier was not found"), 400
        try:
            all_pairs = dao.getAdjNounPairsByRoad(road_id)
            df = __getRanking(all_pairs)
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500


@bp.route('/', methods=["POST"])
def get_adj_noun_pair():
    df = None
    request_data = request.get_json()
    ids = request_data["data"]
    if ids == None:
        return jsonify(error="Bad Request, check the syntax"), 400
    with DataAccess() as dao:
        try:
            all_pairs = dao.getAdjNounPairsByIds(ids)
            df = __getRanking(all_pairs)
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500


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
