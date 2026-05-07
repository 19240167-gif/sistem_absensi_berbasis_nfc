import 'package:flutter/material.dart';

import '../services/api_client.dart';
import '../services/token_service.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _studentCodeController = TextEditingController();
  final _apiClient = ApiClient();
  final _tokenService = TokenService();

  bool _submitting = false;
  String _resultMessage = '';
  bool _resultOk = false;

  @override
  void dispose() {
    _studentCodeController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!(_formKey.currentState?.validate() ?? false)) {
      return;
    }

    setState(() {
      _submitting = true;
      _resultMessage = '';
    });

    final token = await _tokenService.getOrCreateToken();
    final label = await _tokenService.getDeviceLabel();
    final result = await _apiClient.registerDevice(
      token: token,
      studentCode: _studentCodeController.text.trim(),
      deviceLabel: label,
    );

    if (!mounted) {
      return;
    }

    setState(() {
      _submitting = false;
      _resultOk = result.ok;
      _resultMessage = result.message;
    });
  }

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.all(20),
      children: [
        Text('Daftarkan HP', style: Theme.of(context).textTheme.headlineSmall),
        const SizedBox(height: 8),
        Text(
          'Masukkan NIS siswa untuk mendaftarkan token HP.',
          style: Theme.of(context).textTheme.bodyMedium,
        ),
        const SizedBox(height: 20),
        Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextFormField(
                controller: _studentCodeController,
                decoration: const InputDecoration(
                  labelText: 'NIS',
                  hintText: 'Contoh: S001',
                ),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'NIS wajib diisi.';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _submitting ? null : _submit,
                  child: _submitting
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Text('Kirim ke Server'),
                ),
              ),
              const SizedBox(height: 16),
              if (_resultMessage.isNotEmpty)
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: _resultOk
                        ? Colors.green.withOpacity(0.1)
                        : Colors.red.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(_resultMessage),
                ),
            ],
          ),
        ),
        const SizedBox(height: 20),
        Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Text(
              'Jika server mengembalikan status 409 (token sudah dipakai), tekan Ganti Token di Beranda lalu daftar ulang.',
              style: Theme.of(context).textTheme.bodySmall,
            ),
          ),
        ),
      ],
    );
  }
}
