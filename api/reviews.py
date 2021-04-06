from flask import Blueprint, jsonify
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('reviews', __name__, url_prefix='/reviews')

@bp.route('/')
def reviews_():
    df = None
    with DataAccess() as dao:
        try:
            df = dao.getAllSentiments()
        except Exception as e:
            return jsonify(error=msg), 500
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200

@bp.route('/<int:store_id>')
def redirect_to_one_review_by_Store(store_id):
    return reviewsByStore(store_id)

@bp.route('/store/<int:store_id>')
def reviewsByStore(store_id):
    df = None
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return jsonify(error="The store identifier was not found"), 400
        try:
            df = dao.getSentimentsByStore(store_id)
        except Exception as e:
            return jsonify(error=msg), 500
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200

@bp.route('/road/<int:road_id>')
def reviewsByRoad(road_id):
    df = None
    with DataAccess() as dao:
        if road_id not in dao.getRoads().index.to_list():
            return jsonify(error="The road identifier was not found"), 400
        try:
            df = dao.getSentimentsByRoad(road_id)
        except Exception as e:
            return jsonify(error=msg), 500
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200