from flask import Blueprint, jsonify
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('roads', __name__, url_prefix='/roads')

@bp.route('/')
def allRoads():
    df = None
    with DataAccess() as dao:
        try:
            df = dao.getRoads().reset_index()
        except Exception as e:
            return jsonify(error=msg), 400
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200
