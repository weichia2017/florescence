from datetime import datetime, timezone
import pandas as pd
import numpy as np
import os, sys
from flask import request
from flask_cors import CORS
from flask_caching import Cache
from flask import jsonify
from flask import Flask
sys.path.insert(1, 'florescence/src')
from DatabaseAccess import DataAccess
from MasterReview import Master

app = Flask(__name__)
CORS(app)
cache = Cache(app, config={'CACHE_TYPE': 'simple',
                           'CACHE_DEFAULT_TIMEOUT': 300})
LESS_UPDATES = 60*60*24*7
MORE_UPDATES = 60*60*24


@app.route('/')
def hello():
    return jsonify(
        data="Hello!",
        datetime=datetime.now(timezone.utc)
    ), 400


@app.route('/stores/')
@cache.cached(timeout=LESS_UPDATES)
def all_Stores():
    with DataAccess() as dao:
        try:
            df = dao.getStores().reset_index()
        except Exception as e:
            return __response_error(e)
    return __response_ok(df)


@app.route('/stores/<int:store_id>')
@cache.cached(timeout=LESS_UPDATES)
def one_Store(store_id):
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return __response_invalid("The store identifier was not found")
        try:
            df = dao.getStore(store_id).reset_index()
        except Exception as e:
            return __response_error(e)
    return __response_ok(df)


@app.route('/reviews/<int:store_id>')
@cache.cached(timeout=LESS_UPDATES)
def reviews(store_id):
    df = None
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return __response_invalid("The store identifier was not found")
        try:
            df = dao.getSentiments(store_id)
        except Exception as e:
            return __response_error(e)
    return __response_ok(df)

@app.route('/adj_noun_pairs/<int:store_id>')
@cache.cached(timeout=LESS_UPDATES)
def adj_noun_pairs(store_id):
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return __response_invalid("The store identifier was not found")
        try:
            df = Master(dao.getStoreReviews(store_id)).adj_noun_pairs()
        except Exception as e:
            return __response_error(e)
    return __response_ok(df)

@app.route('/adj_noun_pairs/', methods=["POST"])
def get_adj_noun_pair():
    request_data = request.get_json()
    df = pd.json_normalize(request_data, record_path=['data'])
    df = Master(df).adj_noun_pairs()
    return __response_ok(df)

@app.route('/test/adj_noun_pairs/<int:store_id>')
@cache.cached(timeout=LESS_UPDATES)
def test_adj_noun_pairs(store_id):
    df = None
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return __response_invalid("The store identifier was not found")
        try:
            all_pairs = dao.getAdjNounPairs(store_id)
            num_of_noun = 10
            num_of_adj_each = 3
            top_noun = all_pairs.groupby(['noun']).size().reset_index(name='count').sort_values(['count'], ascending=False).head(num_of_noun)
            filtered_nouns = all_pairs[all_pairs.noun.isin(top_noun.noun.to_list())]
            noun_top_adj_ranking = filtered_nouns.groupby(['noun','adj']).size().reset_index(name='count').sort_values(['count'], ascending=False)
            filtered_adj = filtered_nouns[filtered_nouns.adj.isin(noun_top_adj_ranking.adj.to_list())]
            pairs = filtered_adj.groupby(['noun','adj'])['review_id'].apply(','.join).reset_index()
            pairs['review_id'] = pairs.review_id.apply(lambda x: x.split(","))
            pairs['count'] = pairs.review_id.apply(lambda x: len(x))
            df = pairs.groupby(['noun']).apply(lambda x: x.nlargest(num_of_adj_each,['count'], keep='first')).reset_index(drop=True).sort_values(['noun','count'], ascending=False).reset_index(drop=True)
        except Exception as e:
            return __response_error(e)
    return __response_ok(df)

def __response_invalid(msg):
    return jsonify(error=msg), 500


def __response_error(msg):
    return jsonify(error=msg), 400


def __response_ok(df):
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200
