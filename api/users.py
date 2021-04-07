from flask import Blueprint, request, jsonify
from DatabaseAccess import DataAccess
from werkzeug.security import check_password_hash, generate_password_hash
bp = Blueprint('users', __name__, url_prefix='/users')

@bp.route('/create', methods=['POST'])
def create():
    email = request.form.get('email')
    name = request.form.get('name')
    password = request.form.get('password')
    hashed_password = generate_password_hash(password, method='sha256')
    with DataAccess() as dao:
        if len(dao.getUserByEmail(email)) > 0:
            return jsonify(response=False, message="User Exist"), 400
        try:
            results = dao.createUser(email, name, hashed_password)
        except Exception as e:
            return jsonify(error=e), 500
    return jsonify(response=results), 200

@bp.route('/login', methods=['POST'])
def login():
    email = request.form.get('email')
    password = request.form.get('password')
    with DataAccess() as dao:
        try:
            row = dao.getUserByEmail(email)[0]
            if check_password_hash(row['password'], password):
                if row['admin']:
                    return jsonify(user_id=row['user_id'], admin=row['admin']), 200
                return jsonify(user_id=row['user_id'], store_id=row['store_id']), 200
        except Exception as e:
            return jsonify(error=e), 500
    return jsonify(user_id=""), 401

@bp.route('/update/password', methods=['POST'])
def changePassword():
    user_id = request.form.get('user_id')
    password = request.form.get('password')
    newPassword = request.form.get('newPassword')
    hashed_password = generate_password_hash(newPassword, method='sha256')
    with DataAccess() as dao:
        try:
            row = dao.getUserByUserId(user_id)[0]
            if check_password_hash(row['password'], password):
                results = dao.updateUserPassword(row['user_id'], hashed_password)  
                return jsonify(response=results), 200
            else:
                return jsonify(response=False, message="Incorrect existing password"), 400
        except Exception as e:
            return jsonify(error=e), 500
    return jsonify(response=False), 401

@bp.route('/update/store_id', methods=['POST'])
def changeStoreId():
    admin_id = request.form.get('admin_id')
    user_id = request.form.get('user_id')
    store_id = request.form.get('store_id')
    if not adminRights(admin_id):
        return jsonify(error="Invalid Administrative Rights"), 401
    with DataAccess() as dao:
        try:
            results = dao.updateStoreId(user_id, store_id)
        except Exception as e:
            return jsonify(error=e), 500
    return jsonify(response=results), 200

def adminRights(user_id):
    with DataAccess() as dao:
        row = dao.getUserByUserId(user_id)[0]
        return row['admin']
    return False