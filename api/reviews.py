from flask import Blueprint, jsonify
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('reviews', __name__, url_prefix='/reviews')

@bp.route('/')
def all_Reviews():
    df = None
    with DataAccess() as dao:
        try:
            df = dao.getAllSentiments()
        except Exception as e:
            return jsonify(error=msg), 400
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200

@bp.route('/<int:store_id>')
def store_Reviews(store_id):
    df = None
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return __response_invalid("The store identifier was not found")
        try:
            df = dao.getSentiments(store_id)
        except Exception as e:
            return jsonify(error=msg), 400
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200