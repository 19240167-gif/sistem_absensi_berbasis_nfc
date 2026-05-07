import 'dart:convert';

import 'package:http/http.dart' as http;

import '../core/app_config.dart';

class ApiResult {
  ApiResult({
    required this.ok,
    required this.message,
    this.statusCode,
    this.data,
  });

  final bool ok;
  final String message;
  final int? statusCode;
  final Map<String, dynamic>? data;
}

class ApiClient {
  ApiClient({http.Client? client}) : _client = client ?? http.Client();

  final http.Client _client;

  String get baseUrl => AppConfig.apiBaseUrl;

  Duration get timeout => AppConfig.apiTimeout;

  Map<String, String> _jsonHeaders({String? token}) {
    final headers = <String, String>{
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }

    return headers;
  }

  Future<ApiResult> loginStudent({
    required String nis,
    required String birthDate,
  }) async {
    final uri = Uri.parse('$baseUrl/mobile/login/student');

    try {
      final response = await _client
          .post(
            uri,
            headers: _jsonHeaders(),
            body: jsonEncode({'nis': nis, 'birth_date': birthDate}),
          )
          .timeout(timeout);

      final payload = response.body.isNotEmpty
          ? jsonDecode(response.body) as Map<String, dynamic>
          : <String, dynamic>{};

      return ApiResult(
        ok: response.statusCode >= 200 && response.statusCode < 300,
        message: payload['message']?.toString() ?? 'Login selesai.',
        statusCode: response.statusCode,
        data: payload,
      );
    } catch (error) {
      return ApiResult(ok: false, message: 'Gagal konek ke server: $error');
    }
  }

  Future<ApiResult> loginTeacher({
    required String email,
    required String password,
  }) async {
    final uri = Uri.parse('$baseUrl/mobile/login/teacher');

    try {
      final response = await _client
          .post(
            uri,
            headers: _jsonHeaders(),
            body: jsonEncode({'email': email, 'password': password}),
          )
          .timeout(timeout);

      final payload = response.body.isNotEmpty
          ? jsonDecode(response.body) as Map<String, dynamic>
          : <String, dynamic>{};

      return ApiResult(
        ok: response.statusCode >= 200 && response.statusCode < 300,
        message: payload['message']?.toString() ?? 'Login selesai.',
        statusCode: response.statusCode,
        data: payload,
      );
    } catch (error) {
      return ApiResult(ok: false, message: 'Gagal konek ke server: $error');
    }
  }

  Future<ApiResult> registerDevice({
    required String token,
    required String studentCode,
    required String deviceLabel,
  }) async {
    final uri = Uri.parse('$baseUrl/mobile/register');

    try {
      final response = await _client
          .post(
            uri,
            headers: _jsonHeaders(),
            body: jsonEncode({
              'student_code': studentCode,
              'token': token,
              'device_label': deviceLabel,
              'platform': 'android',
            }),
          )
          .timeout(timeout);

      final payload = response.body.isNotEmpty
          ? jsonDecode(response.body) as Map<String, dynamic>
          : <String, dynamic>{};

      if (response.statusCode >= 200 && response.statusCode < 300) {
        return ApiResult(
          ok: true,
          message: payload['message']?.toString() ?? 'Registrasi berhasil.',
          statusCode: response.statusCode,
          data: payload,
        );
      }

      return ApiResult(
        ok: false,
        message: payload['message']?.toString() ?? 'Registrasi gagal.',
        statusCode: response.statusCode,
        data: payload,
      );
    } catch (error) {
      return ApiResult(ok: false, message: 'Gagal konek ke server: $error');
    }
  }

  Future<ApiResult> fetchTeacherAbsences({
    required String token,
    required String date,
  }) async {
    final uri = Uri.parse('$baseUrl/mobile/teacher/absences?date=$date');

    try {
      final response = await _client
          .get(uri, headers: _jsonHeaders(token: token))
          .timeout(timeout);

      final payload = response.body.isNotEmpty
          ? jsonDecode(response.body) as Map<String, dynamic>
          : <String, dynamic>{};

      return ApiResult(
        ok: response.statusCode >= 200 && response.statusCode < 300,
        message: payload['message']?.toString() ?? 'Request selesai.',
        statusCode: response.statusCode,
        data: payload,
      );
    } catch (error) {
      return ApiResult(ok: false, message: 'Gagal konek ke server: $error');
    }
  }

  Future<ApiResult> fetchStudentSummary({
    required String token,
    String? from,
    String? until,
  }) async {
    final query = <String, String>{};
    if (from != null && from.isNotEmpty) {
      query['from'] = from;
    }
    if (until != null && until.isNotEmpty) {
      query['until'] = until;
    }

    final uri = Uri.parse(
      '$baseUrl/mobile/student/summary',
    ).replace(queryParameters: query.isEmpty ? null : query);

    try {
      final response = await _client
          .get(uri, headers: _jsonHeaders(token: token))
          .timeout(timeout);

      final payload = response.body.isNotEmpty
          ? jsonDecode(response.body) as Map<String, dynamic>
          : <String, dynamic>{};

      return ApiResult(
        ok: response.statusCode >= 200 && response.statusCode < 300,
        message: payload['message']?.toString() ?? 'Request selesai.',
        statusCode: response.statusCode,
        data: payload,
      );
    } catch (error) {
      return ApiResult(ok: false, message: 'Gagal konek ke server: $error');
    }
  }
}
