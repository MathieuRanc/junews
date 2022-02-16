import 'dart:io';

import 'package:flutter/material.dart';
import 'package:junews/screen/PageLogin.dart';
import 'package:flutter/services.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:intl/date_symbol_data_local.dart';

class MyHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)
      ..badCertificateCallback =
          (X509Certificate cert, String host, int port) => true;
  }
}

void main() async {
  await dotenv.load(fileName: ".env");
  print(dotenv.env['BASE_URL_API']);

  HttpOverrides.global = new MyHttpOverrides();
  SystemChrome.setSystemUIOverlayStyle(SystemUiOverlayStyle(
    statusBarBrightness: Brightness.dark,
      ));
  initializeDateFormatting().then((_) =>runApp(MyApp()));
  
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      theme: ThemeData(
      textTheme: TextTheme(
          headline6: TextStyle(color: Colors.white))
      ),
      debugShowCheckedModeBanner: false,
      home: LoginDemo(),
    );
  }
}
