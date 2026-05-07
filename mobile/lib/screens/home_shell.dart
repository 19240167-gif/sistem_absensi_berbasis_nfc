import 'package:flutter/material.dart';

import '../services/auth_service.dart';
import 'hce_screen.dart';
import 'home_screen.dart';
import 'login_screen.dart';
import 'register_screen.dart';
import 'teacher_absence_screen.dart';

class HomeShell extends StatefulWidget {
  const HomeShell({super.key});

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  final _authService = AuthService();
  AuthSession? _session;
  bool _loading = true;
  int _index = 0;

  @override
  void initState() {
    super.initState();
    _loadSession();
  }

  Future<void> _loadSession() async {
    final session = await _authService.getSession();
    if (!mounted) {
      return;
    }
    setState(() {
      _session = session;
      _loading = false;
    });
  }

  Future<void> _logout() async {
    await _authService.clearSession();
    if (!mounted) {
      return;
    }
    setState(() {
      _session = null;
      _index = 0;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    if (_session == null) {
      return LoginScreen(onLoggedIn: _loadSession);
    }

    if (_session!.isGuru) {
      return _TeacherShell(session: _session!, onLogout: _logout);
    }

    return _StudentShell(
      session: _session!,
      index: _index,
      onSelectIndex: (value) => setState(() => _index = value),
      onLogout: _logout,
    );
  }
}

class _StudentShell extends StatelessWidget {
  const _StudentShell({
    required this.session,
    required this.index,
    required this.onSelectIndex,
    required this.onLogout,
  });

  final AuthSession session;
  final int index;
  final ValueChanged<int> onSelectIndex;
  final VoidCallback onLogout;

  List<Widget> _pages(AuthSession session) => [
    HomeScreen(authToken: session.token),
    const RegisterScreen(),
    const HceScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Halo, ${session.name}'),
        actions: [
          IconButton(onPressed: onLogout, icon: const Icon(Icons.logout)),
        ],
      ),
      body: SafeArea(child: _pages(session)[index]),
      bottomNavigationBar: NavigationBar(
        selectedIndex: index,
        onDestinationSelected: onSelectIndex,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home),
            label: 'Beranda',
          ),
          NavigationDestination(
            icon: Icon(Icons.how_to_reg_outlined),
            selectedIcon: Icon(Icons.how_to_reg),
            label: 'Daftar HP',
          ),
          NavigationDestination(
            icon: Icon(Icons.nfc_outlined),
            selectedIcon: Icon(Icons.nfc),
            label: 'Mode NFC',
          ),
        ],
      ),
    );
  }
}

class _TeacherShell extends StatelessWidget {
  const _TeacherShell({required this.session, required this.onLogout});

  final AuthSession session;
  final VoidCallback onLogout;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Absensi Guru - ${session.name}'),
        actions: [
          IconButton(onPressed: onLogout, icon: const Icon(Icons.logout)),
        ],
      ),
      body: SafeArea(child: TeacherAbsenceScreen(token: session.token)),
    );
  }
}
