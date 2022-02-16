import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:junews/Common/colors.dart';
import 'package:junews/screen/Page_event.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter/cupertino.dart';
import 'package:junews/screen/settings.dart';
import 'package:junews/Common/Global_variable.dart';

import 'dart:convert';
import 'dart:async';
import 'package:http/http.dart' as http;

Future<_Event> ListeEvent() async {
  await dotenv.load(fileName: ".env");
  final response = await http.get(
    Uri.parse(dotenv.env['BASE_URL_API']! + '/etudiants/feed'),
    headers: {
      "accept": "application/json",
      "Content-Type": "application/merge-patch+json",
      "Authorization": "Bearer " + token,
    },
  );

  if (response.statusCode == 200 || response.statusCode == 201) {
    return _Event.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to Load Evenement');
  }
}

class _Event {
  final List<dynamic> Events;

  _Event({
    required this.Events,
  });

  factory _Event.fromJson(Map<String, dynamic> json) {
    return _Event(
      Events: json['dataOneGrid'],
    );
  }
}

class EventsFeed extends StatefulWidget {
  @override
  _EventsFeedState createState() => _EventsFeedState();
}

class _EventsFeedState extends State<EventsFeed> {
  Icon _searchIcon = Icon(
    Icons.search,
  );
  bool isSearchClicked = false;
  List<String> itemList = [];

  late Future<_Event> events;
  @override
  void initState() {
    super.initState();
    events = ListeEvent();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color.fromRGBO(30, 27, 25, 1),
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            backgroundColor: Color.fromRGBO(30, 27, 25, 1),
            floating: true,
            pinned: false,
            flexibleSpace: FlexibleSpaceBar(
              titlePadding: EdgeInsets.only(left: 20, bottom: 2, right: 50),
              title: Text(
                'Events',
                textAlign: TextAlign.left,
                style: TextStyle(
                  fontSize: 30,
                  color: Color.fromRGBO(255, 255, 255, 1),
                ),
              ),
            ),
            automaticallyImplyLeading: false,
            actions: [
              Padding(
                padding: EdgeInsets.only(top: 13),
                child: IconButton(
                  icon: _searchIcon,
                  onPressed: () {
                    showSearch(
                      context: context,
                      delegate: _SearchBar(events),
                    );
                  },
                  iconSize: 28,
                ),
              ),
              Padding(
                padding: EdgeInsets.only(top: 13),
                child: IconButton(
                  icon: Icon(
                    Icons.settings,
                  ),
                  onPressed: () {
                    Navigator.push(context,
                        MaterialPageRoute(builder: (_) => SettingsScreen()));
                  },
                  iconSize: 22,
                ),
              ),
            ],
          ),
          buildImages(),
        ],
      ),
    );
  }

  Widget buildImages() => SliverToBoxAdapter(
          child: Column(children: [
        FutureBuilder<_Event>(
          future: events,
          builder: (context, snapshot) {
            if (snapshot.hasData) {
              return ListView.builder(
                shrinkWrap: true,
                primary: false,
                physics: NeverScrollableScrollPhysics(),
                itemCount: snapshot.data!.Events.length,
                itemBuilder: (BuildContext context, int index) {
                  return myGridItem(
                    snapshot.data!.Events[index]["presence"],
                    snapshot.data!.Events[index]["jour"],
                    snapshot.data!.Events[index]["mois"].substring(0, 3),
                    snapshot.data!.Events[index]["nomEvent"],
                    snapshot.data!.Events[index]["nbParticipant"].toString(),
                    snapshot.data!.Events[index]["nomAssociation"],
                    snapshot.data!.Events[index]["imageEvent"],
                    snapshot.data!.Events[index]["idEvent"],
                  );
                },
              );
            } else if (snapshot.hasError) {
              return Text("${snapshot.error}");
            }
            return CircularProgressIndicator(
              color: NOIR_FOND,
            );
          },
        ),
        SizedBox(height: 60)
      ]));
}

Widget myGridItem(bool Pres, String jour, String Mois, String gridName,
    String gridName1, String gridName2, String gridimage, int idEvent) {
  return Container(
    height: 400,
    margin: EdgeInsets.symmetric(horizontal: 15, vertical: 7),
    decoration: BoxDecoration(
      color: Color.fromRGBO(158, 158, 165, 1),
      borderRadius: BorderRadius.circular(20),
      image: DecorationImage(
        image: NetworkImage(gridimage),
        fit: BoxFit.cover,
      ),
    ),
    child: Builder(
      builder: (context) {
        final part;
        final color;
        if (Pres) {
          part = 'Inscrit';
          color = Color.fromRGBO(255, 92, 57, 1);
        } else {
          part = 'Non inscrit';
          color = Colors.grey[600];
        }
        return InkWell(
          child: Stack(
            children: <Widget>[
//Couche Gradient partie inférieure
              Container(
                  decoration: BoxDecoration(
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(0),
                  topRight: Radius.circular(0),
                  bottomLeft: Radius.circular(20),
                  bottomRight: Radius.circular(20),
                ),
                gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Color.fromRGBO(0, 0, 0, 0),
                      Color.fromRGBO(0, 0, 0, 0.75),
                      Color.fromRGBO(0, 0, 0, 1)
                    ]),
              )),

//Texte infos evenement
              Container(
                padding: EdgeInsets.all(16.0),
                alignment: Alignment(-1, 0.80),
                child: RichText(
                  text: TextSpan(
                      text: gridName1 + " participants\n",
                      style: TextStyle(
                          color: Color.fromRGBO(216, 216, 216, 1),
                          fontSize: 10,
                          height: 1.5),
                      children: <TextSpan>[
                        TextSpan(
                          text: gridName + "\n",
                          style: TextStyle(
                            color: Color.fromRGBO(158, 158, 165, 1),
                            fontSize: 20,
                          ),
                        ),
                        TextSpan(
                            text: gridName2,
                            style: TextStyle(
                              color: Color.fromRGBO(158, 158, 165, 1),
                              fontSize: 10,
                            ))
                      ]),
                ),
              ),

// Etiquette date de l'Event
              Align(
                alignment: Alignment.topRight,
                child: Container(
                  width: 53,
                  height: 60,
                  margin: EdgeInsets.only(right: 15, top: 15),
                  alignment: Alignment.center,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    color: Color.fromRGBO(255, 255, 255, 1),
                  ),
                  child: Text(
                    jour + '\n' + Mois,
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Color.fromRGBO(0, 0, 0, 1),
                      fontSize: 17,
                    ),
                  ),
                ),
              ),

//Etiquette Participation

              Align(
                alignment: Alignment.topLeft,
                child: Container(
                    width: 72,
                    height: 36,
                    margin: EdgeInsets.only(left: 15, top: 15),
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(10),
                      color: color,
                    ),
                    child: Text(
                      part,
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: Color.fromRGBO(255, 255, 255, 0.8),
                        fontSize: 13,
                      ),
                    )),
              ),
            ],
          ),
          onTap: () {
            Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (context) => Event(
                          idEvent: idEvent,
                        )));
          },
        );
      },
    ),
  );
}

class _SearchBar extends SearchDelegate {
  Future<_Event> event;
  _SearchBar(this.event);

  @override
  ThemeData appBarTheme(BuildContext context) {
    final ThemeData theme = Theme.of(context);
    return theme.copyWith(
      primaryColor: NOIR_FOND,
      inputDecorationTheme: InputDecorationTheme(
          border: InputBorder.none,
          focusedBorder: InputBorder.none,
          enabledBorder: InputBorder.none,
          fillColor: Color.fromRGBO(136, 136, 136, 0.25),
          filled: true,
          hintStyle:
              TextStyle(color: Color.fromRGBO(158, 158, 165, 1), fontSize: 18)),
    );
  }

  @override
  TextStyle? get searchFieldStyle =>
      TextStyle(color: Colors.white, fontSize: 18);
  @override
  String? get searchFieldLabel => "Rechercher";
  List<Widget> buildActions(BuildContext context) {
    return [
      IconButton(
          icon: Icon(Icons.clear),
          onPressed: () {
            query = '';
          })
    ];
  }

  @override
  Widget buildLeading(BuildContext context) {
    return IconButton(
      icon: Icon(
        Icons.arrow_back,
      ),
      onPressed: () {
        close(context, null);
      },
    );
  }

  @override
  Widget buildResults(BuildContext context) {
    if (query.length < 3) {
      return Scaffold(
          backgroundColor: NOIR_FOND,
          body: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: <Widget>[
              Center(
                child: Text("Recherche de plus de deux caractères requise.",
                    style: TextStyle(color: Colors.white, fontSize: 25)),
              )
            ],
          ));
    }

    return Scaffold(
      backgroundColor: NOIR_FOND,
      body: FutureBuilder<_Event>(
        future: event,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            return ListView.builder(
              itemCount: snapshot.data!.Events.length,
              itemBuilder: (BuildContext context, int index) {
                if (query == snapshot.data!.Events[index]["nomEvent"])
                  return Container(
                      margin: EdgeInsets.symmetric(vertical: 9),
                      child: ListTile(
                        title: Text(snapshot.data!.Events[index]["nomEvent"],
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 30,
                            )),
                        subtitle: Text(
                          snapshot.data!.Events[index]["nomAssociation"] +
                              ' - ' +
                              snapshot.data!.Events[index]["nbParticipant"]
                                  .toString() +
                              ' participants',
                          style: TextStyle(
                            color: Color.fromRGBO(216, 216, 216, 1),
                            fontSize: 12,
                          ),
                        ),
                        leading: Container(
                          width: 80,
                          height: 80,
                          decoration: BoxDecoration(
                              image: DecorationImage(
                                  fit: BoxFit.fitHeight,
                                  image: NetworkImage(snapshot
                                      .data!.Events[index]["imageEvent"]))),
                        ),
                        onTap: () {
                          Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) => Event(
                                      idEvent: snapshot.data!.Events[index]
                                          ["idEvent"])));
                        },
                      ));
                else
                  return Container();
              },
            );
          } else if (snapshot.hasError) {
            return Text("${snapshot.error}");
          }
          return CircularProgressIndicator(
            color: NOIR_FOND,
          );
        },
      ),
    );
  }

  @override
  Widget buildSuggestions(BuildContext context) {
    return Scaffold(
      backgroundColor: NOIR_FOND,
      body: FutureBuilder<_Event>(
        future: event,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            return ListView.builder(
              itemCount: snapshot.data!.Events.length,
              itemBuilder: (BuildContext context, int index) {
                return Container(
                    margin: EdgeInsets.symmetric(vertical: 9),
                    child: ListTile(
                      title: Text(snapshot.data!.Events[index]["nomEvent"],
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 30,
                          )),
                      subtitle: Text(
                        snapshot.data!.Events[index]["nomAssociation"] +
                            ' - ' +
                            snapshot.data!.Events[index]["nbParticipant"]
                                .toString() +
                            ' participants',
                        style: TextStyle(
                          color: Color.fromRGBO(216, 216, 216, 1),
                          fontSize: 12,
                        ),
                      ),
                      leading: Container(
                        width: 60,
                        height: 80,
                        decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(10),
                            image: DecorationImage(
                                fit: BoxFit.fill,
                                image: NetworkImage(snapshot.data!.Events[index]
                                    ["imageEvent"]))),
                      ),
                      onTap: () {
                        Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (_) => Event(
                                    idEvent: snapshot.data!.Events[index]
                                        ["idEvent"])));
                      },
                    ));
              },
            );
          } else if (snapshot.hasError) {
            return Text("${snapshot.error}");
          }
          return CircularProgressIndicator(
            color: NOIR_FOND,
          );
        },
      ),
    );
  }
}
