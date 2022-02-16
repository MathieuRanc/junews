import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:junews/Common/Global_variable.dart';
import 'package:junews/Common/NavBar.dart';
import 'package:junews/Common/colors.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:http/http.dart' as http;

Future<Evenement> fetchEvenement(int idEvent) async {
  await dotenv.load(fileName: ".env");
  final response = await http.get(
    Uri.parse(dotenv.env['BASE_URL_API']! +
        '/evenements/' +
        idEvent.toString() +
        '/infos'),
    headers: {
      "accept": "application/json",
      "Content-Type": "application/merge-patch+json",
      "Authorization": "Bearer " + token,
    },
  );
  if (response.statusCode == 200 || response.statusCode == 201) {
    return Evenement.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to Load Evenement');
  }
}

class Evenement {
  final String nom;
  final String description;
  final int nbParticipant;
  final String nomAsso;
  final String dateEvenement;
  final List<dynamic> participant;
  final bool Presence;
  final List<dynamic> listeEvent;
  final String imageEvent;
  final String Lieu;
  final bool Admin;

  Evenement(
      {required this.nom,
      required this.description,
      required this.nbParticipant,
      required this.nomAsso,
      required this.dateEvenement,
      required this.participant,
      required this.Presence,
      required this.listeEvent,
      required this.imageEvent,
      required this.Lieu,
      required this.Admin});

  factory Evenement.fromJson(Map<String, dynamic> json) {
    return Evenement(
        Lieu: json['lieuEvent'],
        description: json['descriptionEvenement'],
        nom: json['nomEvenement'],
        nbParticipant: json['nbEtudiant'],
        nomAsso: json['nomAssociation'],
        dateEvenement: json['dateEvenement'],
        participant: json['listeParticipant'],
        listeEvent: json['listeEventEtudiant'],
        Presence: json['presence'],
        imageEvent: json['imageEvent'],
        Admin: json['administre']);
  }
}

Future<Evenement> update(bool participe, int idEvent) async {
  await dotenv.load(fileName: ".env");
  await http.post(
    Uri.parse(dotenv.env['BASE_URL_API']! +
        "/evenements/" +
        idEvent.toString() +
        '/participe'),
    body: json.encode({"participe": participe}),
    headers: {
      "accept": "application/json",
      "Content-Type": "application/merge-patch+json",
      "Authorization": "Bearer " + token,
    },
  );
  final response = await http.get(
    Uri.parse(dotenv.env['BASE_URL_API']! +
        '/evenements/' +
        idEvent.toString() +
        '/infos'),
    headers: {
      "accept": "application/json",
      "Content-Type": "application/merge-patch+json",
      "Authorization": "Bearer " + token,
    },
  );
  if (response.statusCode == 200 || response.statusCode == 201) {
    return Evenement.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to Load Evenement');
  }
}

Supprimer(int idEvent) async {
  await dotenv.load(fileName: ".env");
  await http.delete(
    Uri.parse(
        dotenv.env['BASE_URL_API']! + "/evenements/" + idEvent.toString()),
    headers: {
      "accept": "application/json",
      "Content-Type": "application/merge-patch+json",
      "Authorization": "Bearer " + token,
    },
  );
}

class Event extends StatefulWidget {
  final idEvent;
  Event({Key? key, @required this.idEvent}) : super(key: key);
  @override
  _EventState createState() => _EventState(idEvent);
}

class _EventState extends State<Event> {
  int idEvent;
  _EventState(this.idEvent);

  late Future<Evenement> futureEvenement;
  late bool participe;

  @override
  void initState() {
    super.initState();
    futureEvenement = fetchEvenement(idEvent);
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Scaffold(
        extendBodyBehindAppBar: true,
        floatingActionButtonLocation: FloatingActionButtonLocation.startTop,
        floatingActionButton: Card(
          elevation: 10,
          margin: EdgeInsets.all(10),
          shadowColor: Colors.black,
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(35)),
          child: InkWell(
            borderRadius: BorderRadius.circular(35),
            onTap: () {
              Navigator.pushAndRemoveUntil(
                  context,
                  MaterialPageRoute(builder: (_) => NavBAr()),
                  (route) => false);
            },
            child: SvgPicture.asset('asset/Image/arrow_back.svg'),
          ),
        ),
        backgroundColor: NOIR_FOND,
        body: Center(
          child: SingleChildScrollView(
            child: Column(
              children: <Widget>[
                FutureBuilder<Evenement>(
                  future: futureEvenement,
                  builder: (context, snapshot) {
                    if (snapshot.hasData) {
                      return Container(
                        height: 350,
                        alignment: Alignment.bottomLeft,
                        decoration: BoxDecoration(
                            image: DecorationImage(
                          image: NetworkImage(snapshot.data!.imageEvent),
                          fit: BoxFit.cover,
                        )),
                        child: Stack(children: <Widget>[
                          Container(
                            alignment: Alignment.bottomCenter,
                            child: Container(
                              height: 194,
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                    begin: Alignment.bottomCenter,
                                    end: Alignment.topCenter,
                                    colors: [
                                      NOIR_FOND,
                                      Color.fromRGBO(30, 27, 25, 0),
                                    ]),
                              ),
                            ),
                          ),
                          Container(
                            padding: EdgeInsets.only(left: 15),
                            alignment: Alignment(-1, 0.95),
                            child: RichText(
                              text: TextSpan(
                                  text: 'Organisé par: ' +
                                      snapshot.data!.nomAsso +
                                      '\n',
                                  style: TextStyle(
                                      color: Color.fromRGBO(216, 216, 216, 1),
                                      fontSize: 16,
                                      height: 3),
                                  children: <TextSpan>[
                                    TextSpan(
                                        //text: 'Nom de l\'event\n',
                                        text: (snapshot.data!.nom).toString() +
                                            '\n',
                                        style: TextStyle(
                                          color:
                                              Color.fromRGBO(216, 216, 216, 1),
                                          fontSize: 16,
                                          fontWeight: FontWeight.bold,
                                        )),
                                    TextSpan(
                                      text: (snapshot.data!.nbParticipant)
                                              .toString() +
                                          ' participants',
                                      style: TextStyle(
                                          color:
                                              Color.fromRGBO(216, 216, 216, 1),
                                          fontSize: 10,
                                          height: 2),
                                    ),
                                  ]),
                            ),
                          ),
                        ]),
                      );
                    } else if (snapshot.hasError) {
                      return Text("${snapshot.error}");
                    }
                    return CircularProgressIndicator();
                  },
                ),
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 15),
                  alignment: Alignment.centerLeft,
                  child: FutureBuilder<Evenement>(
                    future: futureEvenement,
                    builder: (context, snapshot) {
                      if (snapshot.hasData) {
                        return Text(
                          snapshot.data!.dateEvenement,
                          style: TextStyle(
                              color: Color.fromRGBO(255, 255, 255, 1),
                              fontSize: 16,
                              height: 2),
                        );
                      } else if (snapshot.hasError) {
                        return Text("${snapshot.error}");
                      }
                      return CircularProgressIndicator();
                    },
                  ),
                ),
                _buildCounterButton(),
                SizedBox(height: 15),
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 15),
                  alignment: Alignment.centerLeft,
                  child: FutureBuilder<Evenement>(
                    future: futureEvenement,
                    builder: (context, snapshot) {
                      if (snapshot.hasData) {
                        return RichText(
                          text: TextSpan(
                              text: 'A propos\n\n',
                              style: TextStyle(
                                  color: Color.fromRGBO(255, 255, 255, 1),
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold),
                              children: <TextSpan>[
                                TextSpan(
                                    text: snapshot.data!.description,
                                    style: TextStyle(
                                      color: Color.fromRGBO(216, 216, 216, 1),
                                      fontSize: 16,
                                    ))
                              ]),
                        );
                      } else if (snapshot.hasError) {
                        return Text("${snapshot.error}");
                      }
                      return CircularProgressIndicator();
                    },
                  ),
                ),
                SizedBox(height: 20),
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 15),
                  alignment: Alignment.centerLeft,
                  child: FutureBuilder<Evenement>(
                    future: futureEvenement,
                    builder: (context, snapshot) {
                      if (snapshot.hasData) {
                        if (snapshot.data!.Lieu != 'false') {
                          return RichText(
                            text: TextSpan(
                                text: 'Lieu : ',
                                style: TextStyle(
                                  color: Color.fromRGBO(255, 255, 255, 1),
                                  fontSize: 20,
                                ),
                                children: <TextSpan>[
                                  TextSpan(
                                      text: snapshot.data!.Lieu,
                                      style: TextStyle(
                                        color: Color.fromRGBO(216, 216, 216, 1),
                                        fontSize: 18,
                                      ))
                                ]),
                          );
                        } else {
                          return SizedBox(height: 20);
                        }
                      } else if (snapshot.hasError) {
                        return Text("${snapshot.error}");
                      }
                      return CircularProgressIndicator(
                        color: NOIR_FOND,
                      );
                    },
                  ),
                ),
                SizedBox(height: 20),
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 15),
                  alignment: Alignment.centerLeft,
                  child: Text(
                    ' Participants',
                    style: TextStyle(
                      color: Color.fromRGBO(255, 255, 255, 1),
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                FutureBuilder<Evenement>(
                  future: futureEvenement,
                  builder: (context, snapshot) {
                    if (snapshot.hasData) {
                      return ListView.builder(
                          shrinkWrap: true,
                          primary: false,
                          physics: NeverScrollableScrollPhysics(),
                          itemCount: snapshot.data!.nbParticipant,
                          itemBuilder: (BuildContext context, int index) {
                            return Container(
                                margin: EdgeInsets.symmetric(vertical: 10),
                                child: ListTile(
                                  leading: Container(
                                    width: 80,
                                    decoration: BoxDecoration(
                                      shape: BoxShape.circle,
                                      image: DecorationImage(
                                        image: NetworkImage(
                                            snapshot.data!.participant[index]
                                                ["photoEtudiant"]),
                                        fit: BoxFit.contain,
                                      ),
                                    ),
                                  ),
                                  title: Text(
                                      snapshot.data!.participant[index]
                                          ["identite"],
                                      style: TextStyle(
                                        color: Colors.white,
                                        fontSize: 18,
                                      )),
                                ));
                          });
                    } else if (snapshot.hasError) {
                      return Text("${snapshot.error}");
                    }
                    return CircularProgressIndicator();
                  },
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildCounterButton() {
    return FutureBuilder<Evenement>(
      future: futureEvenement,
      builder: (context, snapshot) {
        if (snapshot.hasData) {
          participe = snapshot.data!.Presence;
          if (!snapshot.data!.Admin) {
            if (participe) {
              return Container(
                padding: EdgeInsets.symmetric(horizontal: 15),
                margin: EdgeInsets.only(top: 5.0),
                height: 50,
                width: 370,
                decoration: BoxDecoration(
                  color: Color.fromRGBO(255, 92, 57, 1),
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
                    participe = false;
                    _ButtonPress();
                  },
                  child: Text(
                    'Se désinscrire',
                    style: TextStyle(
                      color: Color.fromRGBO(216, 216, 216, 1),
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              );
            } else {
              return Container(
                padding: EdgeInsets.symmetric(horizontal: 15),
                margin: EdgeInsets.only(top: 5.0),
                height: 50,
                width: 370,
                decoration: BoxDecoration(
                  color: NOIR_FOND,
                  borderRadius: BorderRadius.circular(4),
                  border: Border.all(
                    color: Color.fromRGBO(216, 216, 216, 1),
                    width: 1,
                  ),
                  boxShadow: [
                    BoxShadow(
                        color: Color.fromRGBO(0, 0, 0, 0.25),
                        offset: Offset(-4, 4),
                        blurRadius: 10)
                  ],
                ),
                child: TextButton(
                  onPressed: () {
                    participe = true;
                    _ButtonPress();
                  },
                  child: Text(
                    'S\'inscrire',
                    style: TextStyle(
                      color: Color.fromRGBO(216, 216, 216, 1),
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              );
            }
          } else {
            return Container(
              padding: EdgeInsets.symmetric(horizontal: 15),
              margin: EdgeInsets.only(top: 5.0),
              height: 50,
              width: 370,
              decoration: BoxDecoration(
                color: Colors.red,
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
                  Supprimer(idEvent);
                  Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(builder: (_) => NavBAr()),
                      (route) => false);
                },
                child: Text(
                  'Supprimer l\'évenement',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            );
          }
        } else if (snapshot.hasError) {
          return Text("${snapshot.error}");
        }
        return CircularProgressIndicator(
          color: NOIR_FOND,
        );
      },
    );
  }

  void _ButtonPress() {
    setState(() {
      futureEvenement = update(participe, idEvent);
    });
  }
}
