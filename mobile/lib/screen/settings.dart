import 'package:flutter/material.dart';
import 'package:junews/Common/colors.dart';
import 'package:settings_ui/settings_ui.dart';
import 'package:junews/screen/PageLogin.dart';

class SettingsScreen extends StatefulWidget {
  @override
  _SettingsScreenState createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  bool lockInBackground = true;
  bool notificationsEnabled = true;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: Text('Paramètres'),
        backgroundColor: Color.fromRGBO(0, 0, 0, 0.1),
      ),
      body: buildSettingsList(),
    );
  }

  Widget buildSettingsList() {
    return SettingsList(
      backgroundColor: NOIR_FOND,
      sections: [
        CustomSection(child: SizedBox(height: 15)),
        SettingsSection(
          title: 'Mon compte',
          titleTextStyle: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            fontSize: 17,
          ),
          tiles: [
            SettingsTile(
          switchActiveColor: NOIR_FOND,
                onPressed: (context) {
                  Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(builder: (_) => LoginDemo()),
                      (Route<dynamic> route) => false);
                },
                title: 'Se déconnecter',
                titleTextStyle: TextStyle(color: Colors.white),
                leading: Icon(Icons.exit_to_app, color: Colors.white)),
          ],
        ),
        CustomSection(
          child: Column(
            children: [
              Padding(
                padding: const EdgeInsets.only(top: 22, bottom: 8),
                child: Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                      image: DecorationImage(
                          image: AssetImage('asset/Image/ic_launcher.png'),
                          fit: BoxFit.contain)),
                ),
              ),
              Text(
                'Version: 1.0.0',
                style: TextStyle(color: Color(0xFF777777)),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
