from flask import Flask
import sys, json
sys.path.insert(1, 'florescence/src') 
from DatabaseAccess import DataAccess
app = Flask(__name__)

@app.route('/')
def hello():
    return 'Hello, World!'

@app.route('/reviews/<int:store_id>')
def reviewsForStore(store_id):
    dao = DataAccess()
    df = dao.getStoreReviews(store_id)
    result = df.to_json(orient="records")
    parsed = json.loads(result)
    return json.dumps(parsed, indent=4)

@app.route('/sentiments/<int:store_id>')
def sentimentsForStore(store_id):
    dao = DataAccess()
    raw_df = dao.getStoreReviews(store_id)
    c = Cleaner(raw_df)
    c.separateEmptyReview()
    c.remove_translated()
    df = c.get_df()
    result = raw_df.to_json(orient="records")
    parsed = json.loads(result)
    return json.dumps(parsed, indent=4)