from flask import Blueprint
from werkzeug.security import check_password_hash, generate_password_hash
bp = Blueprint('auth', __name__, url_prefix='/auth')

@bp.route('/signup', methods=['POST'])
def signup():
    email = request.form.get('email')
    password = request.form.get('password')
    hashed_password = generate_password_hash(password, method='sha256')
    return jsonify(
        password=password,
        hashed_password=hashed_password,
        check=check_password_hash(hashed_password, password),
        datetime=datetime.now(timezone.utc)
    ), 400

# TODO: Login

# TODO: Profile

# TODO: Update Profile