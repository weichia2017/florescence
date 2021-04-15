from flask import Blueprint, jsonify
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('stores', __name__, url_prefix='/stores')


@bp.route('/')
def all_Stores():
    with DataAccess() as dao:
        try:
            df = dao.getStores().reset_index()
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(error="Server Error"), 500


@bp.route('/<int:store_id>')
def redirect_to_one_store(store_id):
    return storesByID(store_id)


@bp.route('/store/<int:store_id>')
def storesByID(store_id):
    df = None
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return jsonify(error="The store identifier was not found"), 400
        try:
            df = dao.getStore(store_id).reset_index()
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500


@bp.route('/road/<int:road_id>')
def storesByRoad(road_id):
    df = None
    with DataAccess() as dao:
        if road_id not in dao.getRoads().index.to_list():
            return jsonify(error="The road identifier was not found"), 400
        try:
            df = dao.getStoreByRoad(road_id).reset_index()
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500
