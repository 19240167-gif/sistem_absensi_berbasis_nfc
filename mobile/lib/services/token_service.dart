import 'dart:io';

import 'package:device_info_plus/device_info_plus.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:uuid/uuid.dart';

class TokenService {
  static const _tokenKey = 'nfc_token';
  static const _storage = FlutterSecureStorage();
  static const _uuid = Uuid();

  Future<String> getOrCreateToken() async {
    final existing = await _storage.read(key: _tokenKey);
    if (existing != null && existing.isNotEmpty) {
      return existing;
    }

    final token = _uuid.v4().replaceAll('-', '');
    await _storage.write(key: _tokenKey, value: token);
    return token;
  }

  Future<String> rotateToken() async {
    final token = _uuid.v4().replaceAll('-', '');
    await _storage.write(key: _tokenKey, value: token);
    return token;
  }

  Future<String> getDeviceLabel() async {
    final info = DeviceInfoPlugin();

    if (Platform.isAndroid) {
      final android = await info.androidInfo;
      return '${android.brand} ${android.model}'.trim();
    }

    return 'Unknown device';
  }
}
