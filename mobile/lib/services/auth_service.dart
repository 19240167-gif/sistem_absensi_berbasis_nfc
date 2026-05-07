import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class AuthSession {
  const AuthSession({
    required this.token,
    required this.role,
    required this.name,
  });

  final String token;
  final String role;
  final String name;

  bool get isGuru => role == 'guru';
  bool get isSiswa => role == 'siswa';
}

class AuthService {
  static const _storage = FlutterSecureStorage();
  static const _tokenKey = 'auth_token';
  static const _roleKey = 'auth_role';
  static const _nameKey = 'auth_name';

  Future<AuthSession?> getSession() async {
    final token = await _storage.read(key: _tokenKey);
    final role = await _storage.read(key: _roleKey);
    final name = await _storage.read(key: _nameKey);

    if (token == null || token.isEmpty || role == null || role.isEmpty) {
      return null;
    }

    return AuthSession(token: token, role: role, name: name ?? '-');
  }

  Future<void> saveSession({
    required String token,
    required String role,
    required String name,
  }) async {
    await _storage.write(key: _tokenKey, value: token);
    await _storage.write(key: _roleKey, value: role);
    await _storage.write(key: _nameKey, value: name);
  }

  Future<void> clearSession() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _roleKey);
    await _storage.delete(key: _nameKey);
  }
}
