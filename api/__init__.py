from flask import Flask
import sys, json
sys.path.insert(1, 'Florescence/src') 
from DatabaseAccess import DataAccess
app = Flask(__name__)

@app.route('/')
def hello():
    return 'Hello, World!'

@app.route('/reviews/<int:store_id>')
def reviewsForStore(store_id):
    dao = DataAccess()
    raw_df = dao.getStoreReviews(store_id)
    result = raw_df.to_json(orient="records")
    parsed = json.loads(result)
    return json.dumps(parsed, indent=4)