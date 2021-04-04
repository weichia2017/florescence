from flask import Blueprint
from DatabaseAccess import DataAccess
from werkzeug.security import check_password_hash, generate_password_hash
bp = Blueprint('users', __name__, url_prefix='/users')

@bp.route('/signup', methods=['POST'])
def signup():
    email = request.form.get('email')
    password = request.form.get('password')
    hashed_password = generate_password_hash(password, method='sha256')
    with DataAccess() as dao:
        try:
            df = dao.createUsers(username, password)
        except Exception as e:
            return jsonify(error=msg), 400
    return jsonify(response="OK"), 400

# TODO: Login

# TODO: Profile

# TODO: Update Profile