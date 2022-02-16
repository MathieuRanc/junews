import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:junews/Common/NavBar.dart';
import 'package:junews/Common/colors.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:junews/Common/Global_variable.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;

class LoginDemo extends StatefulWidget {
  @override
  _LoginDemoState createState() => _LoginDemoState();
}

class _LoginDemoState extends State<LoginDemo> {
  bool _isSecret = true;
  String _email = '';
  String _password = '';
  String Error_msg = '';

  final _formKey = GlobalKey<FormState>();

  Login(String email, String pass) async {
    await dotenv.load(fileName: ".env");
    Map body = {"username": email, "password": pass};
    SharedPreferences sharedPreferences = await SharedPreferences.getInstance();
    var jsonresp;
    final response = await http.post(
        Uri.parse(dotenv.env['BASE_URL_API']! + '/login_check'),
        body: jsonEncode(body),
        headers: {
          "Accept": "application/json",
          "Content-Type": "application/json",
        });
    if (response.statusCode == 200 || response.statusCode == 201) {
      jsonresp = json.decode(response.body);

      if (jsonresp != null) {
        sharedPreferences.setString("token", jsonresp['token']);
        token = jsonresp['token'];
        SharedPreferences sharedPreferences2 =
            await SharedPreferences.getInstance();
        final response2 = await http.get(
          Uri.parse(dotenv.env['BASE_URL_API']! + '/etudiants/navbar'),
          headers: {
            "accept": "application/json",
            "Content-Type": "application/merge-patch+json",
            "Authorization": "Bearer " + token,
          },
        );
        if (response2.statusCode == 200 || response2.statusCode == 201) {
          var resp = jsonDecode(response2.body);

          sharedPreferences2.setInt("administre", resp['administre']);
          idAsso = resp['administre'];
          Navigator.pushAndRemoveUntil(
              context,
              MaterialPageRoute(builder: (_) => NavBAr()),
              (Route<dynamic> route) => false);
        } else {
          throw Exception('Failed to Load Nav');
        }
      }
    } else {
      setState(() => Error_msg = 'Identifiant ou Mot de passe incorrect');
      throw Exception('Failed to Load Login');
    }
  }

  String mdp_oubl = 'Mot de passe oublié ?';
  bool is_clicked = false;
  Color color = Colors.white;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      primary: true,
      backgroundColor: NOIR_FOND,
      body: Center(
        child: SingleChildScrollView(
          padding: EdgeInsets.symmetric(horizontal: 15),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: <Widget>[
              Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      Container(
                        padding:
                            EdgeInsets.symmetric(vertical: 50, horizontal: 25),
                        child: SvgPicture.asset('asset/Image/logo_junews.svg'),
                      ),
                      Container(
                        alignment: Alignment.centerLeft,
                        child: RichText(
                          text: TextSpan(
                              text: 'Bienvenue 👋\n',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 30,
                              ),
                              children: <TextSpan>[
                                TextSpan(
                                    text: 'Connecte toi à ton compte',
                                    style: TextStyle(
                                      color: Color.fromRGBO(158, 158, 165, 1),
                                      fontSize: 16,
                                    ))
                              ]),
                        ),
                      ),
                      SizedBox(height: 20),
                      TextFormField(
                        onChanged: (value) => setState(() => _email = value),
                        validator: (value) =>
                            value!.isEmpty ? 'Please enter a valid id' : null,
                        style:
                            TextStyle(color: Color.fromRGBO(255, 255, 255, 1)),
                        decoration: InputDecoration(
                          isDense: true,
                          filled: true,
                          fillColor: Color.fromRGBO(58, 58, 60, 1),
                          hintText: 'Identifiant',
                          hintStyle: TextStyle(
                            color: Color.fromRGBO(158, 158, 165, 1),
                            fontSize: 16,
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(4),
                            borderSide: BorderSide(
                              color: Color.fromRGBO(58, 58, 60, 1),
                            ),
                          ),
                          focusedBorder: OutlineInputBorder(
                              borderSide: BorderSide(
                            color: Color.fromRGBO(58, 58, 60, 1),
                          )),
                        ),
                      ),
                      SizedBox(
                        height: 10,
                      ),
                      TextFormField(
                        onChanged: (value) => setState(() => _password = value),
                        validator: (value) => value!.length < 3
                            ? '3 caracters min required'
                            : null,
                        style: TextStyle(color: Colors.white),
                        obscureText: _isSecret,
                        decoration: InputDecoration(
                          isDense: true,
                          suffixIcon: InkWell(
                            onTap: () => setState(() => _isSecret = !_isSecret),
                            child: Icon(
                              !_isSecret
                                  ? (Icons.visibility)
                                  : Icons.visibility_off,
                              color: Colors.grey[400],
                            ),
                          ),
                          filled: true,
                          fillColor: Color.fromRGBO(58, 58, 60, 1),
                          hintText: 'Mot de passe',
                          hintStyle: TextStyle(
                            color: Color.fromRGBO(158, 158, 165, 1),
                            fontSize: 16,
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(4),
                            borderSide: BorderSide(
                              color: Color.fromRGBO(58, 58, 60, 1),
                            ),
                          ),
                          focusedBorder: OutlineInputBorder(
                              borderSide: BorderSide(
                            color: Color.fromRGBO(58, 58, 60, 1),
                          )),
                        ),
                      ),
                      TextButton(
                        onPressed: () {
                          setState(() {
                            is_clicked = !is_clicked;
                          });
                          if (is_clicked) {
                            setState(() {
                              mdp_oubl =
                                  'Veuillez contacter: assistance@junews.fr';
                              color = Colors.orange;
                            });
                          } else {
                            setState(() {
                              mdp_oubl = 'Mot de passe oublié ?';
                              color = Colors.white;
                            });
                          }
                        },
                        child: Text(
                          mdp_oubl,
                          style: TextStyle(
                            color: color,
                            fontSize: 14,
                          ),
                        ),
                      ),
                      Container(
                        margin: EdgeInsets.only(top: 5.0),
                        height: 50,
                        width: 340,
                        decoration: BoxDecoration(
                          color: ORANGE,
                          borderRadius: BorderRadius.circular(4),
                          boxShadow: [
                            BoxShadow(
                                color: Color.fromRGBO(0, 0, 0, 0.25),
                                offset: Offset(-4, 4),
                                blurRadius: 10)
                          ],
                        ),
                        child: TextButton(
                          onPressed: () {
                            if (_formKey.currentState!.validate()) {
                              print('id=' + _email);
                              print('mdp=' + _password);
                              Login(_email, _password);
                            }
                          },
                          child: Text(
                            'Se connecter',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 16,
                            ),
                          ),
                        ),
                      ),
                      Text(Error_msg,
                          style: TextStyle(
                              color: Colors.red, height: 2, fontSize: 20)),
                    ],
                  )),
            ],
          ),
        ),
      ),
    );
  }
}
