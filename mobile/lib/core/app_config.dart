import 'package:flutter_dotenv/flutter_dotenv.dart';

class AppConfig {
  static String get apiBaseUrl =>
      dotenv.env['API_BASE_URL'] ?? 'http://10.0.2.2:8000/api';

  static Duration get apiTimeout {
    final value = int.tryParse(dotenv.env['API_TIMEOUT_SECONDS'] ?? '10');
    return Duration(seconds: value ?? 10);
  }
}
