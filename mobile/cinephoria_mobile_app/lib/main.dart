import 'package:flutter/material.dart';
import 'pages/seances_page.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Cinephoria Mobile',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: SeancesPage(),
    );
  }
}
