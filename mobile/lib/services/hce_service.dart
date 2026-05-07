import 'package:flutter/services.dart';

class HceService {
  static const MethodChannel _channel = MethodChannel('com.absensi.nfc/hce');

  Future<void> setToken(String token) async {
    await _channel.invokeMethod('setToken', {'token': token});
  }

  Future<void> setEnabled(bool enabled) async {
    await _channel.invokeMethod('setEnabled', {'enabled': enabled});
  }
}
