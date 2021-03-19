from flask import Flask
from flask import jsonify
from flask_caching import Cache
from flask_cors import CORS
from flask import request

import sys, os
import numpy as np
import pandas as pd
from datetime import datetime, timezone
sys.path.insert(1, 'florescence/src') 
from DatabaseAccess import DataAccess
from MasterReview import Master

app = Flask(__name__)
CORS(app)
cache = Cache(app, config=
              {
                  'CACHE_TYPE': 'simple',
                  'CACHE_DEFAULT_TIMEOUT': 300
              }
             )
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

# @app.route('/reviews/<int:store_id>')
# @cache.cached(timeout=LESS_UPDATES)
# def sentiment(store_id):
#     with DataAccess() as dao:
#         if store_id not in dao.getStores().index.to_list():
#             return __response_invalid("The store identifier was not found")
#         try:
#             df = Master(dao.getStoreReviews(store_id)).sentiment_scores()
#         except Exception as e:
#             return __response_error(e)
#     return __response_ok(df)

@app.route('/reviews/<int:store_id>')
@cache.cached(timeout=LESS_UPDATES)
def test(store_id):
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

def __response_invalid(msg):
    return jsonify(error=msg), 500

def __response_error(msg):
    return jsonify(error=msg), 400

def __response_ok(df):
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200
