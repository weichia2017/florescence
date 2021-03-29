from flask import Blueprint, jsonify
from DatabaseAccess import DataAccess
from datetime import datetime, timezone

bp = Blueprint('stores', __name__, url_prefix='/stores')

@bp.route('/')
def all_Stores():
    with DataAccess() as dao:
        try:
            df = dao.getStores().reset_index()
        except Exception as e:
            return jsonify(error=msg), 400
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200

@bp.route('/<int:store_id>')
def one_Store(store_id):
    with DataAccess() as dao:
        if store_id not in dao.getStores().index.to_list():
            return __response_invalid("The store identifier was not found")
        try:
            df = dao.getStore(store_id).reset_index()
        except Exception as e:
            return jsonify(error=msg), 400
    return jsonify(
        data=df.to_dict('records'),
        datetime=datetime.now(timezone.utc)
    ), 200