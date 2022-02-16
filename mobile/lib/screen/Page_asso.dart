import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:junews/Common/colors.dart';
import 'package:junews/screen/Page_event.dart';
import 'dart:convert';
import 'package:junews/Common/Global_variable.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:http/http.dart' as http;
import 'package:junews/screen/Creation_event/Form_event.dart';

Future<Asso> fetchAsso(int idAsso) async {
  await dotenv.load(fileName: ".env");
  final response = await http.get(
    Uri.parse(dotenv.env['BASE_URL_API']! +
        '/associations/' +
        idAsso.toString()+'/infos'),
    headers: {
      "accept": "application/json",
      "Content-Type": "application/merge-patch+json",
      "Authorization": "Bearer " + token,
    },
  );

  if (response.statusCode == 200) {
    return Asso.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to Load Asso');
  }
}

class Asso {
  final String id;
  final String nomAsso;
  final String logo;
  final String description;
  final List<dynamic> event;
  final List<dynamic> membres;
  // ignore: non_constant_identifier_names
  final bool Administre;

  Asso({
    required this.id,
    required this.nomAsso,
    required this.logo,
    required this.description,
    required this.event,
    required this.membres,
    // ignore: non_constant_identifier_names
    required this.Administre,
  });

  factory Asso.fromJson(Map<String, dynamic> json) {
    return Asso(
      id: json['id'],
      description: json['descriptionAssociation'],
      nomAsso: json["nomAssociation"],
      logo: json["logoAssociation"],
      event: json["listeEvenements"],
      membres: json["ListeMembres"],
      Administre: json["administre"],
    );
  }
}

class Home extends StatefulWidget {
  final idAsso, afficher;
  Home({Key? key, @required this.idAsso, @required this.afficher})
      : super(key: key);
  @override
  State<StatefulWidget> createState() {
    return new _Home(idAsso, afficher);
  }
}

class _Home extends State<Home> {
  late Future<Asso> futureAsso;

  int idAsso;
  bool afficher;
  _Home(this.idAsso, this.afficher);

  @override
  void initState() {
    super.initState();
    futureAsso = fetchAsso(idAsso);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      floatingActionButtonLocation: FloatingActionButtonLocation.startTop,
      floatingActionButton: afficher ?Card(
          elevation: 10,
          margin: EdgeInsets.all(10),
          shadowColor: Colors.black,
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(35)),
          child: InkWell(
            borderRadius: BorderRadius.circular(35),
            onTap: () {
              Navigator.pop(context);
            },
            child: SvgPicture.asset('asset/Image/arrow_back.svg'),
          ),
        ):null,
      backgroundColor: NOIR_FOND,
      body: SingleChildScrollView(
        padding: EdgeInsets.symmetric(horizontal: 20),
        child: Column(children: <Widget>[
          afficher ? SizedBox(height: 80) : SizedBox(height: 40),
          FutureBuilder<Asso>(
              future: futureAsso,
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  return Row(
                    children: <Widget>[
                      Container(
                        width: 90,
                        height: 90,
                        decoration: BoxDecoration(
                          image: DecorationImage(
                              image: NetworkImage(snapshot.data!.logo),
                              fit: BoxFit.contain),
                        ),
                      ),
                      SizedBox(width: 20),
                      Container(
                        alignment: Alignment.center,
                        child: Text(
                          snapshot.data!.nomAsso,
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 18,
                          ),
                        ),
                      )
                    ],
                  );
                } else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                return CircularProgressIndicator(
                  color: NOIR_FOND,
                );
              }),
          new Container(
            margin: EdgeInsets.all(15.0),
            child: FutureBuilder<Asso>(
              future: futureAsso,
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  return Text(
                    snapshot.data!.description,
                    style: TextStyle(
                      fontSize: 16,
                      color: Colors.white,
                    ),
                  );
                } else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                return CircularProgressIndicator(color: NOIR_FOND);
              },
            ),
          ),
          SizedBox(height: 7),

          FutureBuilder<Asso>(
              future: futureAsso,
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  if (snapshot.data!.Administre) {
                    return new Container(
                        child: new TextButton(
                            onPressed: () {
                              Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                      builder: (context) => Form_event(
                                          nomAsso: snapshot.data!.id)));
                            },
                            child: new Container(
                              alignment: Alignment.center,
                              width: 334,
                              height: 50,
                              decoration: BoxDecoration(
                                  borderRadius:
                                      BorderRadius.all(Radius.circular(4)),
                                  color: Color.fromRGBO(255, 92, 57, 1)),
                              child: Text('Ajouter un évènement ',
                                  textAlign: TextAlign.center,
                                  style: new TextStyle(
                                    fontSize: 16,
                                    color: Colors.white,
                                  )),
                            )));
                  } else {
                    return SizedBox(height: 10);
                  }
                } else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                return CircularProgressIndicator(color: NOIR_FOND);
              }),
          SizedBox(height: 8),
          new Container(
            alignment: Alignment.centerLeft,
            child: Text('Evènements',
                textAlign: TextAlign.left,
                style: new TextStyle(
                  fontSize: 20,
                  color: Colors.white,
                )),
          ),
          // vos event
          FutureBuilder<Asso>(
              future: futureAsso,
              builder: (context, snapshot) {
                if (snapshot.hasData) {
                  return ListView.builder(
                      shrinkWrap: true,
                      primary: false,
                      physics: NeverScrollableScrollPhysics(),
                      padding: const EdgeInsets.symmetric(vertical: 15),
                      itemCount: snapshot.data!.event.length,
                      itemBuilder: (BuildContext context, int index) {
                        return ListTile(
                          // ignore: deprecated_member_use
                          leading: new Container(
                            width: 80,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(4),
                              image: DecorationImage(
                                image: NetworkImage(
                                    snapshot.data!.event[index]['imageEvent']),
                                fit: BoxFit.cover,
                              ),
                            ),
                          ),
                          title:
                              Text(snapshot.data!.event[index]["nomEvenement"],
                                  style: new TextStyle(
                                    color: Colors.white,
                                    fontSize: 12,
                                  )),
                          subtitle: Text(
                            snapshot.data!.event[index]["dateEvenement"],
                            style: new TextStyle(
                              color: Colors.white,
                              fontSize: 12,
                            ),
                          ),
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) => Event(
                                      idEvent: snapshot.data!.event[index]
                                          ["id"])),
                            );
                          },
                        );
                      });
                } else if (snapshot.hasError) {
                  return Text("${snapshot.error}");
                }
                return CircularProgressIndicator(color: NOIR_FOND);
              }),
          //Liste des membres de l'asso
          Container(
            alignment: Alignment.centerLeft,
            child: Text(
              "Membres de l'asso",
              style: new TextStyle(
                color: Colors.white,
                fontSize: 20,
              ),
            ),
          ),
          FutureBuilder<Asso>(
            future: futureAsso,
            builder: (context, snapshot) {
              if (snapshot.hasData) {
                return ListView.builder(
                    shrinkWrap: true,
                    primary: false,
                    physics: NeverScrollableScrollPhysics(),
                    itemCount: snapshot.data!.membres.length,
                    itemBuilder: (BuildContext context, int compt) {
                      return Container(
                          margin: EdgeInsets.symmetric(vertical: 10),
                          child: ListTile(
                            title: Text(
                              snapshot.data!.membres[compt]["identite"],
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 18,
                              ),
                            ),
                            leading: Container(
                              width: 80,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                image: DecorationImage(
                                  fit: BoxFit.contain,
                                  image: NetworkImage(snapshot
                                      .data!.membres[compt]["photoEtudiant"]),
                                ),
                              ),
                            ),
                          ));
                    });
              } else if (snapshot.hasError) {
                return Text("${snapshot.error}");
              }
              return CircularProgressIndicator(color: NOIR_FOND);
            },
          ),
          SizedBox(height: 60),
        ]),
      ),
    );
  }
}
