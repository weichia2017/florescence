from flask import Blueprint, jsonify
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('roads', __name__, url_prefix='/roads')

@bp.route('/')
def Roads():
    df = None
    with DataAccess() as dao:
        try:
            df = dao.getRoads().reset_index()
        except Exception as e:
            return jsonify(error=msg), 500
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200

@bp.route('/<int:road_id>')
def Road(road_id):
    df = None
    with DataAccess() as dao:
        try:
            df = dao.getRoad(road_id).reset_index()
        except Exception as e:
            return jsonify(error=msg), 500
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200