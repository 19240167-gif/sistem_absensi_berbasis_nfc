import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../services/api_client.dart';
import '../services/token_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key, required this.authToken});

  final String authToken;

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final _tokenService = TokenService();
  final _apiClient = ApiClient();

  bool _loading = true;
  bool _summaryLoading = true;
  String _token = '';
  String _deviceLabel = '';
  String _summaryPeriod = '';
  String _summaryMessage = '';
  Map<String, int> _summary = const {
    'hadir': 0,
    'izin': 0,
    'sakit': 0,
    'alfa': 0,
    'total': 0,
  };

  @override
  void initState() {
    super.initState();
    _loadToken();
    _loadSummary();
  }

  Future<void> _loadToken() async {
    setState(() => _loading = true);
    final token = await _tokenService.getOrCreateToken();
    final label = await _tokenService.getDeviceLabel();
    if (!mounted) {
      return;
    }
    setState(() {
      _token = token;
      _deviceLabel = label;
      _loading = false;
    });
  }

  Future<void> _loadSummary() async {
    setState(() {
      _summaryLoading = true;
      _summaryMessage = '';
    });

    final result = await _apiClient.fetchStudentSummary(
      token: widget.authToken,
    );

    if (!mounted) {
      return;
    }

    if (!result.ok || result.data == null) {
      setState(() {
        _summaryLoading = false;
        _summaryMessage = result.message;
      });
      return;
    }

    final data = result.data ?? <String, dynamic>{};
    final summary = Map<String, dynamic>.from(data['summary'] ?? {});
    final period = Map<String, dynamic>.from(data['period'] ?? {});

    setState(() {
      _summaryLoading = false;
      _summaryMessage = result.message;
      _summary = {
        'hadir': (summary['hadir'] ?? 0) as int,
        'izin': (summary['izin'] ?? 0) as int,
        'sakit': (summary['sakit'] ?? 0) as int,
        'alfa': (summary['alfa'] ?? 0) as int,
        'total': (summary['total'] ?? 0) as int,
      };
      final from = period['from']?.toString() ?? '';
      final until = period['until']?.toString() ?? '';
      _summaryPeriod = from.isNotEmpty && until.isNotEmpty
          ? '$from s/d $until'
          : '';
    });
  }

  Future<void> _copyToken() async {
    await Clipboard.setData(ClipboardData(text: _token));
    if (!mounted) {
      return;
    }
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Token disalin ke clipboard.')),
    );
  }

  Future<void> _rotateToken() async {
    final token = await _tokenService.rotateToken();
    if (!mounted) {
      return;
    }
    setState(() => _token = token);
    ScaffoldMessenger.of(
      context,
    ).showSnackBar(const SnackBar(content: Text('Token baru sudah dibuat.')));
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: () async {
        await _loadToken();
        await _loadSummary();
      },
      child: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          Text(
            'Absensi NFC Siswa',
            style: Theme.of(context).textTheme.headlineSmall,
          ),
          const SizedBox(height: 8),
          Text(
            'Token perangkat ini akan dipakai untuk absensi NFC/HCE.',
            style: Theme.of(context).textTheme.bodyMedium,
          ),
          const SizedBox(height: 20),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: _loading
                  ? const Center(child: CircularProgressIndicator())
                  : Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Token NFC',
                          style: Theme.of(context).textTheme.titleMedium,
                        ),
                        const SizedBox(height: 8),
                        SelectableText(
                          _token,
                          style: Theme.of(context).textTheme.bodyLarge
                              ?.copyWith(fontFamily: 'monospace'),
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            ElevatedButton.icon(
                              onPressed: _copyToken,
                              icon: const Icon(Icons.copy),
                              label: const Text('Salin'),
                            ),
                            const SizedBox(width: 12),
                            OutlinedButton.icon(
                              onPressed: _rotateToken,
                              icon: const Icon(Icons.refresh),
                              label: const Text('Ganti Token'),
                            ),
                          ],
                        ),
                      ],
                    ),
            ),
          ),
          const SizedBox(height: 16),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: _summaryLoading
                  ? const Center(child: CircularProgressIndicator())
                  : Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Ringkasan Kehadiran',
                          style: Theme.of(context).textTheme.titleMedium,
                        ),
                        if (_summaryPeriod.isNotEmpty)
                          Padding(
                            padding: const EdgeInsets.only(top: 4),
                            child: Text(
                              _summaryPeriod,
                              style: Theme.of(context).textTheme.bodySmall,
                            ),
                          ),
                        if (_summaryMessage.isNotEmpty)
                          Padding(
                            padding: const EdgeInsets.only(top: 8),
                            child: Text(
                              _summaryMessage,
                              style: Theme.of(context).textTheme.bodySmall,
                            ),
                          ),
                        const SizedBox(height: 12),
                        Wrap(
                          spacing: 12,
                          runSpacing: 12,
                          children: [
                            _SummaryChip(
                              label: 'Hadir',
                              value: _summary['hadir'] ?? 0,
                              color: Colors.green,
                            ),
                            _SummaryChip(
                              label: 'Izin',
                              value: _summary['izin'] ?? 0,
                              color: Colors.orange,
                            ),
                            _SummaryChip(
                              label: 'Sakit',
                              value: _summary['sakit'] ?? 0,
                              color: Colors.amber,
                            ),
                            _SummaryChip(
                              label: 'Alfa',
                              value: _summary['alfa'] ?? 0,
                              color: Colors.red,
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Text(
                          'Total catatan: ${_summary['total'] ?? 0}',
                          style: Theme.of(context).textTheme.bodyMedium,
                        ),
                      ],
                    ),
            ),
          ),
          const SizedBox(height: 16),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Perangkat',
                    style: Theme.of(context).textTheme.titleMedium,
                  ),
                  const SizedBox(height: 8),
                  Text(_deviceLabel.isEmpty ? '-' : _deviceLabel),
                  const SizedBox(height: 8),
                  Text(
                    'Jika ganti HP, tekan Ganti Token lalu daftar ulang.',
                    style: Theme.of(context).textTheme.bodySmall,
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _SummaryChip extends StatelessWidget {
  const _SummaryChip({
    required this.label,
    required this.value,
    required this.color,
  });

  final String label;
  final int value;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: color.withOpacity(0.12),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 8,
            height: 8,
            decoration: BoxDecoration(color: color, shape: BoxShape.circle),
          ),
          const SizedBox(width: 6),
          Text('$label: $value'),
        ],
      ),
    );
  }
}
