from flask import Flask
from flask import jsonify
from flask_caching import Cache

import sys
import numpy as np
import pandas as pd
from datetime import datetime, timezone
sys.path.insert(1, 'florescence/src') 
from DatabaseAccess import DataAccess
from MasterReview import Master

app = Flask(__name__)
cache = Cache(app, config={'CACHE_TYPE': 'simple'})
LIGHT_CACHE = 60
HEAVY_CACHE = 60*60

@app.route('/')
def hello():
    return jsonify(
        data="Hello!",
        datetime=datetime.now(timezone.utc)
    ), 200

@app.route('/store/')
@cache.cached(timeout=LIGHT_CACHE)
def all_Stores():
    with DataAccess() as dao:
        try:
            df = dao.getStores()
        except Exception as e:
            return jsonify(
                data=str(e)
            ), 500
    return jsonify(
        data=df.to_dict('index'),
        datetime=datetime.now(timezone.utc)
    ), 200

@app.route('/store/<int:store_id>')
@cache.cached(timeout=LIGHT_CACHE)
def one_Store(store_id):
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return jsonify(data="The Store Identifier was not found"), 400
        try:
            df = dao.getStore(store_id)
        except Exception as e:
            return jsonify(
                data=str(e)
        ), 500
    return jsonify(
        data=df.to_dict('index'),
        datetime=datetime.now(timezone.utc)
    ), 200

@app.route('/sentiment/<int:store_id>')
@cache.cached(timeout=HEAVY_CACHE)
def sentiment(store_id):
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return jsonify(respond="Not a valid Store ID"), 400
        try:
            df = Master(dao.getStoreReviews(store_id)).sentiment_scores()
        except Exception as e:
            return jsonify(
                respond=str(e)
        ), 500
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200

@app.route('/adj_noun_pairs/<int:store_id>')
@cache.cached(timeout=HEAVY_CACHE)
def adj_noun_pairs(store_id):
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return jsonify(respond="Not a valid Store ID"), 400
        try:
            df = Master(dao.getStoreReviews(store_id)).adj_noun_pairs()
        except Exception as e:
            return jsonify(
                respond=str(e)
        ), 500
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200

