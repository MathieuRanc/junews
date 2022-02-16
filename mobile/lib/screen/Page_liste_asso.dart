import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:junews/Common/Global_variable.dart';
import 'package:junews/Common/colors.dart';
import 'package:junews/screen/Page_asso.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;

Future<Associations> fetchAssociation() async {
  await dotenv.load(fileName: ".env");
  final response = await http
      .get(Uri.parse(dotenv.env['BASE_URL_API']! + '/associations/infos'), 
      headers: {
      'accept': 'application/json',
      "Authorization": "Bearer " + token,
  });
  if (response.statusCode == 200) {
    return Associations.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to Load Asso');
  }
}

class Associations {
  final List<dynamic> association;

  Associations({
    required this.association,
  });

  factory Associations.fromJson(Map<String, dynamic> json) {
    return Associations(
      association: json['associations'],
    );
  }
}

class ListeAsso extends StatefulWidget {
  @override
  _ListeAssoState createState() => _ListeAssoState();
}

class _ListeAssoState extends State<ListeAsso> {
  late Future<Associations> futureAssociation;
  Icon _searchIcon = Icon(
    Icons.search,
    size: 28,
  );

  @override
  void initState() {
    super.initState();
    futureAssociation = fetchAssociation();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        backgroundColor: NOIR_FOND,
        body: CustomScrollView(
          slivers: [
            SliverAppBar(
              backgroundColor: Color.fromRGBO(30, 27, 25, 1),
              floating: true,
              pinned: false,
              flexibleSpace: FlexibleSpaceBar(
                titlePadding: EdgeInsets.only(left: 20, bottom: 2, right: 50),
                title: Text(
                  'Liste des associations',
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
                        delegate: _SearchBar(futureAssociation),
                      );
                    },
                    iconSize: 22,
                  ),
                ),
              ],
            ),
            buildList()
          ],
        ),
      );

  Widget buildList() => SliverToBoxAdapter(
          child: Column(children: [
        FutureBuilder<Associations>(
          future: futureAssociation,
          builder: (context, snapshot) {
            if (snapshot.hasData) {
              return ListView.builder(
                  shrinkWrap: true,
                  primary: false,
                  physics: NeverScrollableScrollPhysics(),
                  padding: const EdgeInsets.all(16),
                  itemCount: snapshot.data!.association.length,
                  itemBuilder: (BuildContext context, int index) {
                    return myListItem(
                        snapshot.data!.association[index]['nomAssociation'],
                        snapshot.data!.association[index]["nombreMembre"],
                        snapshot.data!.association[index]["logo"],
                        snapshot.data!.association[index]["idAssociation"]);
                  });
            } else if (snapshot.hasError) {
              return Text("${snapshot.error}");
            }
            return CircularProgressIndicator(color: NOIR_FOND);
          },
        ),
        SizedBox(height: 40)
      ]));

  Widget myListItem(
      String listAsso, int listMembres, String listImage, int idAsso) {
    return Container(
      width: 335,
      height: 85,
      margin: EdgeInsets.all(10),
      // decoration: BoxDecoration(
      //   color: Color.fromRGBO(158, 158, 165, 1),
      //   borderRadius: BorderRadius.circular(20),
      // ),
      child: Builder(builder: (context) {
        return InkWell(
          child: Stack(children: <Widget>[
            Container(
                width: 100,
                height: 100,
                padding: EdgeInsets.all(80.0),
                decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    //borderRadius: BorderRadius.circular(100),
                    image: DecorationImage(
                        image: NetworkImage(listImage), fit: BoxFit.cover))),

            // ignore: deprecated_member_use

            Container(
              margin: EdgeInsets.only(left: 100),
              child: RichText(
                text: TextSpan(
                    text: listAsso + '\n',
                    style: TextStyle(
                        color: Color.fromRGBO(255, 255, 255, 1),
                        fontSize: 20,
                        height: 2),
                    children: <TextSpan>[
                      TextSpan(
                        text: 'Association',
                        style: TextStyle(
                            color: Color.fromRGBO(158, 158, 165, 1),
                            fontSize: 15,),
                      ),
                      TextSpan(
                          text: ' - ' + (listMembres).toString() + ' membres',
                          style: TextStyle(
                              color: Color.fromRGBO(158, 158, 165, 1),
                              fontSize: 15,))
                    ]),
              ),
            )
          ]),
          onTap: () {
            Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (_) => Home(
                          idAsso: idAsso,
                          afficher: true,
                        )));
          },
        );
      }),
    );
  }
}

class _SearchBar extends SearchDelegate {
  Future<Associations> event;
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
          hintStyle: TextStyle(
              color: Color.fromRGBO(158, 158, 165, 1),
              fontSize: 18)),
    );
  }

  @override
  TextStyle? get searchFieldStyle => TextStyle(
      color: Color.fromRGBO(158, 158, 165, 1),
      fontSize: 18);
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
      body: FutureBuilder<Associations>(
        future: event,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            return ListView.builder(
              itemCount: snapshot.data!.association.length,
              itemBuilder: (BuildContext context, int index) {
                if (query ==
                    snapshot.data!.association[index]["nomAssociation"])
                  return Container(
                      margin: EdgeInsets.symmetric(vertical: 9),
                      child: ListTile(
                        title: Text(
                            snapshot.data!.association[index]["nomAssociation"],
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 20,
                            )),
                        subtitle: Text(
                          snapshot.data!.association[index]["nombreMembre"].toString()+' membres',
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
                                  fit: BoxFit.contain,
                                  image: NetworkImage(snapshot
                                      .data!.association[index]["logo"]))),
                        ),
                        onTap: () {
                          Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) =>
                                      Home(idAsso: 1, afficher: true)));
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
      body: FutureBuilder<Associations>(
        future: event,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            return ListView.builder(
              itemCount: snapshot.data!.association.length,
              itemBuilder: (BuildContext context, int index) {
                return Container(
                    margin: EdgeInsets.symmetric(vertical: 9),
                    child: ListTile(
                      title: Text(
                          snapshot.data!.association[index]["nomAssociation"],
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 20,
                          )),
                      subtitle: Text(
                        snapshot.data!.association[index]["nombreMembre"].toString()+' membres',
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
                                fit: BoxFit.contain,
                                image: NetworkImage(snapshot
                                    .data!.association[index]["logo"]))),
                      ),
                      onTap: () {
                        Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (_) =>
                                    Home(idAsso: 1, afficher: true)));
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
