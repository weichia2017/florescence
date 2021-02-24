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

@app.route('/sentiments/<int:store_id>')
def reviewsForStore(store_id):
    try:
        df = Master(dao.getStoreReviews(store_id)).sentimentsForStore()
    except:
        return {}, 400
    return __parse_df(df), 200

def __parse_df(df):
    results = df.to_dict('records')
    return jsonify(rows=len(results),data=results)