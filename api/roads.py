from flask import Blueprint, jsonify
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('roads', __name__, url_prefix='/roads')

@bp.route('/')
def Roads():
    with DataAccess() as dao:
        try:
            df = dao.getRoads().reset_index()
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500


@bp.route('/<int:road_id>')
def Road(road_id):
    df = None
    with DataAccess() as dao:
        try:
            df = dao.getRoad(road_id).reset_index()
            return jsonify(data=df.to_dict('records')), 200
        except Exception as e:
            return jsonify(response="Server Error", error=e), 500
    return jsonify(response="Server Error"), 500
