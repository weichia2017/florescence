from flask import Flask
import sys, json
sys.path.insert(1, 'Florescence/src') 
from DatabaseAccess import DataAccess
app = Flask(__name__)

@app.route('/')
def hello():
    return 'Hello, World!'

@app.route('/reviews')
def reviews():
    dao = DataAccess()
    raw_df = dao.getAllRawReviews()
    result = raw_df.to_json(orient="records")
    parsed = json.loads(result)
    return json.dumps(parsed, indent=4)