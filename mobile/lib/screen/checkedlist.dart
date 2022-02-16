import 'package:flutter/material.dart';

void main() => runApp(const MyApp());

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      home: Scaffold(
        body: const Center(
          child: MyStatefulWidget(),
        ),
      ),
    );
  }
}

class MyStatefulWidget extends StatefulWidget {
  const MyStatefulWidget({Key? key}) : super(key: key);

  @override
  State<MyStatefulWidget> createState() => _MyStatefulWidgetState();
}

class _MyStatefulWidgetState extends State<MyStatefulWidget> {

  // Need that in the controller
  List<String> _promo = ['CSI3', 'CNB3', 'CIR3', 'M1', 'M2'];
  List<bool> check = [false, false, false, false, false];

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
        shrinkWrap: true,
        primary: false,
        physics: NeverScrollableScrollPhysics(),
        padding: const EdgeInsets.all(15),
        itemCount: _promo.length,
        itemBuilder: (BuildContext context, int compt) {
          return CheckboxListTile(
            title: Text("${_promo[compt]}"),
            value: check[compt],
            onChanged: (bool? value) {
              setState(() {
                check[compt] = !check[compt];
                print(check);
              });
            },
          );
        });
  }
}
