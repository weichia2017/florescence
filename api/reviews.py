from flask import Blueprint, jsonify
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('reviews', __name__, url_prefix='/reviews')


@bp.route('/')
def reviews_():
    with DataAccess() as dao:
        try:
            df = dao.getAllSentiments()
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500


@bp.route('/<int:store_id>')
def redirect_to_one_review_by_Store(store_id):
    return reviewsByStore(store_id)


@bp.route('/store/<int:store_id>')
def reviewsByStore(store_id):
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return jsonify(error="The store identifier was not found"), 400
        try:
            df = dao.getSentimentsByStore(store_id)
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500


@bp.route('/road/<int:road_id>')
def reviewsByRoad(road_id):
    with DataAccess() as dao:
        if road_id not in dao.getRoads().index.to_list():
            return jsonify(error="The road identifier was not found"), 400
        try:
            df = dao.getSentimentsByRoad(road_id)
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500
