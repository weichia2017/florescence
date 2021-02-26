from flask import Flask
from flask import jsonify
import sys
import numpy as np
import pandas as pd
sys.path.insert(1, 'florescence/src') 
from DatabaseAccess import DataAccess
from MasterReview import Master

app = Flask(__name__)
dao = DataAccess()

@app.route('/')
def hello():
    return 'Hello, World!'

@app.route('/store/')
def all_Stores():
    df = dao.getStores().reset_index()
    return __parse_df(df), 200

@app.route('/store/<int:store_id>')
def one_Store(store_id):
    df = dao.getStore(store_id).reset_index()
    return __parse_df(df), 200

@app.route('/sentiment/<int:store_id>')
def sentiment(store_id):
    try:
        df = Master(dao.getStoreReviews(store_id)).sentiment_scores()
    except Exception as e:
        return jsonify(respond=str(e)), 400
    return __parse_df(df), 200

@app.route('/adj_noun_pairs/<int:store_id>')
def adj_noun_pairs(store_id):
    try:
        df = Master(dao.getStoreReviews(store_id)).adj_noun_pairs()
    except Exception as e:
        return jsonify(respond=str(e)), 400
    return __parse_df(df), 200

def __parse_df(df):
    results = df.to_dict('records')
    return jsonify(rows=len(results),data=results)