import sys
sys.path.insert(1, 'florescence/src')
from datetime import datetime, timezone
from flask import Flask
from flask_cors import CORS

# HTTP Error Codes
# 200 OK
# 201 Created
# 400 Bad Request
# 401 Unauthorized
# 405 Method Not Allowed
# 500 Server Error

def create_app():
    app = Flask(__name__)
    CORS(app)

    from . import stores, reviews, adj_noun_pairs, roads, users
    app.register_blueprint(stores.bp)
    app.register_blueprint(reviews.bp)
    app.register_blueprint(adj_noun_pairs.bp)
    app.register_blueprint(roads.bp)
    app.register_blueprint(users.bp)
    
    return app