import sys
sys.path.insert(1, 'florescence/src')
from datetime import datetime, timezone
from flask import Flask
from flask_cors import CORS

def create_app():
    app = Flask(__name__)
    CORS(app)

    from . import stores, reviews, adj_noun_pairs, roads
    app.register_blueprint(stores.bp)
    app.register_blueprint(reviews.bp)
    app.register_blueprint(adj_noun_pairs.bp)
    app.register_blueprint(roads.bp)
    
    return app