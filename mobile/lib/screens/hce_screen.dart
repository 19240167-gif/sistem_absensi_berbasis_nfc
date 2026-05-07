import 'package:flutter/material.dart';

import '../services/hce_service.dart';
import '../services/token_service.dart';

class HceScreen extends StatefulWidget {
  const HceScreen({super.key});

  @override
  State<HceScreen> createState() => _HceScreenState();
}

class _HceScreenState extends State<HceScreen> {
  final _tokenService = TokenService();
  final _hceService = HceService();

  bool _enabled = false;
  bool _working = false;

  Future<void> _toggle(bool value) async {
    setState(() => _working = true);
    try {
      final token = await _tokenService.getOrCreateToken();
      await _hceService.setToken(token);
      await _hceService.setEnabled(value);
    } catch (error) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal mengaktifkan HCE: $error')),
        );
      }
    }
    if (!mounted) {
      return;
    }
    setState(() {
      _enabled = value;
      _working = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.all(20),
      children: [
        Text('Mode NFC/HCE', style: Theme.of(context).textTheme.headlineSmall),
        const SizedBox(height: 8),
        Text(
          'Aktifkan agar HP bisa ditap ke NFC reader admin.',
          style: Theme.of(context).textTheme.bodyMedium,
        ),
        const SizedBox(height: 20),
        SwitchListTile(
          value: _enabled,
          onChanged: _working ? null : _toggle,
          title: const Text('Aktifkan NFC HCE'),
          subtitle: Text(_enabled ? 'Siap ditap ke reader.' : 'Belum aktif.'),
        ),
        const SizedBox(height: 20),
        Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Text(
              'Langkah: 1) Aktifkan toggle. 2) Buka layar ini. 3) Tempelkan HP ke reader NFC admin sampai berhasil terdeteksi.',
              style: Theme.of(context).textTheme.bodySmall,
            ),
          ),
        ),
      ],
    );
  }
}
