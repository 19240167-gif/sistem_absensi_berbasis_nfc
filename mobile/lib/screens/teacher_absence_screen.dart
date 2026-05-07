import 'package:flutter/material.dart';

import '../services/api_client.dart';

class TeacherAbsenceScreen extends StatefulWidget {
  const TeacherAbsenceScreen({super.key, required this.token});

  final String token;

  @override
  State<TeacherAbsenceScreen> createState() => _TeacherAbsenceScreenState();
}

class _TeacherAbsenceScreenState extends State<TeacherAbsenceScreen> {
  final _apiClient = ApiClient();

  bool _loading = true;
  String _message = '';
  String _selectedDate = '';
  List<Map<String, dynamic>> _items = [];

  @override
  void initState() {
    super.initState();
    _selectedDate = _today();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _message = '';
    });

    final result = await _apiClient.fetchTeacherAbsences(
      token: widget.token,
      date: _selectedDate,
    );

    if (!mounted) {
      return;
    }

    setState(() {
      _loading = false;
      _message = result.message;
      _items = (result.data?['absences'] as List<dynamic>? ?? [])
          .map((item) => Map<String, dynamic>.from(item as Map))
          .toList();
    });
  }

  Future<void> _pickDate() async {
    final now = DateTime.now();
    final current = DateTime.parse(_selectedDate);

    final picked = await showDatePicker(
      context: context,
      initialDate: current,
      firstDate: DateTime(now.year - 1),
      lastDate: DateTime(now.year + 1),
    );

    if (picked != null) {
      setState(() => _selectedDate = _formatDate(picked));
      await _load();
    }
  }

  String _today() => _formatDate(DateTime.now());

  String _formatDate(DateTime date) {
    final month = date.month.toString().padLeft(2, '0');
    final day = date.day.toString().padLeft(2, '0');
    return '${date.year}-$month-$day';
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: _load,
      child: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  'Siswa Tidak Hadir',
                  style: Theme.of(context).textTheme.headlineSmall,
                ),
              ),
              TextButton.icon(
                onPressed: _pickDate,
                icon: const Icon(Icons.date_range),
                label: Text(_selectedDate),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            _message.isEmpty ? 'Daftar siswa yang tidak hadir.' : _message,
            style: Theme.of(context).textTheme.bodyMedium,
          ),
          const SizedBox(height: 20),
          if (_loading)
            const Center(child: CircularProgressIndicator())
          else if (_items.isEmpty)
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Text(
                  'Tidak ada siswa yang absen pada tanggal ini.',
                  style: Theme.of(context).textTheme.bodyMedium,
                ),
              ),
            )
          else
            ..._items.map((item) {
              final status = item['status']?.toString() ?? 'belum_absen';
              return Card(
                margin: const EdgeInsets.only(bottom: 12),
                child: ListTile(
                  title: Text(item['student_name']?.toString() ?? '-'),
                  subtitle: Text(
                    '${item['classroom'] ?? '-'} • NIS ${item['nis'] ?? '-'}',
                  ),
                  trailing: _StatusBadge(status: status),
                ),
              );
            }),
        ],
      ),
    );
  }
}

class _StatusBadge extends StatelessWidget {
  const _StatusBadge({required this.status});

  final String status;

  @override
  Widget build(BuildContext context) {
    Color color;
    String label;

    switch (status) {
      case 'izin':
        color = Colors.orange;
        label = 'Izin';
        break;
      case 'sakit':
        color = Colors.amber;
        label = 'Sakit';
        break;
      case 'alfa':
        color = Colors.red;
        label = 'Alfa';
        break;
      default:
        color = Colors.grey;
        label = 'Belum absen';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        label,
        style: TextStyle(color: color, fontWeight: FontWeight.w600),
      ),
    );
  }
}
