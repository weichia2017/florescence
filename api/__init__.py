import sys
sys.path.insert(1, 'florescence/src')
from datetime import datetime, timezone
from flask import Flask
from flask_cors import CORS
#from flask_caching import Cache

def create_app():
    app = Flask(__name__)
    CORS(app)
    #cache = Cache(app, config={'CACHE_TYPE': 'simple', 'CACHE_DEFAULT_TIMEOUT': 3600})

    from . import stores, reviews, adj_noun_pairs
    app.register_blueprint(stores.bp)
    app.register_blueprint(reviews.bp)
    app.register_blueprint(adj_noun_pairs.bp)
    
    return app