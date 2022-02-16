import 'dart:convert';
import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:junews/Common/colors.dart';
import 'package:junews/screen/EventsFeed.dart';
import 'package:junews/screen/Page_asso.dart';
import 'package:junews/screen/Page_liste_asso.dart';
import 'package:http/http.dart' as http;
import 'Global_variable.dart';

//connexion au connecteur de l'API
Future<Nav> fetchNav() async {
  await dotenv.load(fileName: ".env");
  final response = await http.get(
    Uri.parse(dotenv.env['BASE_URL_API']! + '/etudiants/navbar'),
    headers: {
      "accept": "application/json",
      "Content-Type": "application/merge-patch+json",
      "Authorization": "Bearer " + token,
    },
  );
  if (response.statusCode == 200 || response.statusCode == 201) {
    return Nav.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to Load Nav');
  }
}

class Nav {
  final int Administre;

  Nav({
    required this.Administre,
  });

  factory Nav.fromJson(Map<String, dynamic> jSon) {
    return Nav(Administre: jSon["administre"]);
  }
}

class NavBAr extends StatefulWidget {
  @override
  _NavBar createState() => _NavBar();
}

/// This is the private State class that goes with MyStatefulWidget.
class _NavBar extends State<NavBAr> {
  int _selectedIndex = 0;

  late Future<Nav> futureNav;
  void initState() {
    super.initState();
    futureNav = fetchNav();
  }

  final List<Widget> _admin = <Widget>[
    EventsFeed(),
    Home(idAsso: idAsso, afficher: false),
    ListeAsso(),
  ];

  final List<Widget> _non_admin = <Widget>[
    EventsFeed(),
    ListeAsso(),
  ];

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    // Si la personne administre une asso
    return Scaffold(
      backgroundColor: NOIR_FOND,
      body: FutureBuilder<Nav>(
        future: futureNav,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            if (snapshot.data!.Administre > 0) {
              return Scaffold(
                body: Stack(
                  fit: StackFit.expand,
                  children: <Widget>[
                    Align(
                      alignment: Alignment.topCenter,
                      child: _admin.elementAt(_selectedIndex),
                    ),
                    Align(
                      alignment: Alignment.bottomCenter,
                      child: ClipRect(
                        clipBehavior: Clip.hardEdge,
                        child: Container(
                          height: MediaQuery.of(context).size.height / 12,
                          child: BackdropFilter(
                            filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
                            child: BottomNavigationBar(
                              type: BottomNavigationBarType.fixed,
                              elevation: 0,
                              backgroundColor: Color.fromRGBO(60, 60, 60, 0.5),
                              items: const <BottomNavigationBarItem>[
                                BottomNavigationBarItem(
                                  icon: Icon(Icons.event),
                                  label: 'Events',
                                ),
                                BottomNavigationBarItem(
                                  icon: Icon(
                                      Icons.arrow_drop_down_circle_outlined),
                                  label: 'Mon Asso',
                                ),
                                BottomNavigationBarItem(
                                  icon: Icon(Icons.view_list_rounded),
                                  label: 'Liste Assos',
                                ),
                              ],
                              currentIndex: _selectedIndex,
                              selectedItemColor: ORANGE,
                              unselectedItemColor: GRIS_BASIC,
                              onTap: _onItemTapped,
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              );
            } else {
              //S'il n'administre personne
              return Scaffold(
                body: Stack(
                  fit: StackFit.expand,
                  children: <Widget>[
                    Align(
                      alignment: Alignment.topCenter,
                      child: _non_admin.elementAt(_selectedIndex),
                    ),
                    Align(
                        alignment: Alignment.bottomCenter,
                        child: ClipRect(
                          clipBehavior: Clip.hardEdge,
                          child: Container(
                            height: MediaQuery.of(context).size.height / 12,
                            child: BackdropFilter(
                              filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
                              child: Align(
                                alignment: Alignment.bottomCenter,
                                child: BottomNavigationBar(
                                  type: BottomNavigationBarType.fixed,
                                  elevation: 0,
                                  backgroundColor:
                                      Color.fromRGBO(60, 60, 60, 0.5),
                                  items: const <BottomNavigationBarItem>[
                                    BottomNavigationBarItem(
                                      icon: Icon(Icons.event),
                                      label: 'Events',
                                    ),
                                    BottomNavigationBarItem(
                                      icon: Icon(Icons.view_list_rounded),
                                      label: 'Liste Assos',
                                    ),
                                  ],
                                  currentIndex: _selectedIndex,
                                  selectedItemColor: ORANGE,
                                  unselectedItemColor: GRIS_BASIC,
                                  onTap: _onItemTapped,
                                ),
                              ),
                            ),
                          ),
                        )),
                  ],
                ),
              );
            }
          } else if (snapshot.hasError) {
            return Text("${snapshot.error}");
          }
          return Align(
              alignment: Alignment.center,
              child: CircularProgressIndicator(color: Colors.black));
        },
      ),
    );
  }
}
