import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:image_picker/image_picker.dart';
import 'package:junews/Common/colors.dart';
import 'dart:convert';
import 'package:junews/Common/Global_variable.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:http/http.dart' as http;
import 'package:junews/screen/Creation_event/Form_event_2.dart';

Future<Info> fetchPromo() async {
  await dotenv.load(fileName: ".env");
  final response = await http.get(
    Uri.parse(dotenv.env['BASE_URL_API']! + '/evenements/infos'),
    headers: {
      "Authorization": "Bearer " + token,
    },
  );
  if (response.statusCode == 200 || response.statusCode == 201) {
    return Info.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to Load Promo');
  }
}

class Info {
  final List<dynamic> Promos;
  final List<dynamic> Lieux;

  Info({
    required this.Promos,
    required this.Lieux,
  });

  factory Info.fromJson(Map<String, dynamic> json) {
    return Info(Promos: json['promotions'], Lieux: json['lieux']);
  }
}

class Form_event extends StatefulWidget {
  final nomAsso;
  Form_event({Key? key, @required this.nomAsso}) : super(key: key);
  @override
  _Form_event createState() => _Form_event(nomAsso);
}

class _Form_event extends State<Form_event> {
  String nomAsso;
  _Form_event(this.nomAsso);

  late Future<Info> futureInfo;
  @override
  void initState() {
    super.initState();
    futureInfo = fetchPromo();
  }

  File? _image;
  final picker = ImagePicker();

  Future getImage() async {
    final pickedFile = await picker.getImage(source: ImageSource.gallery);

    setState(() {
      if (pickedFile != null) {
        _image = File(pickedFile.path);
        print(_image);
      } else {
        print('No image selected.');
      }
    });
  }

  String _nom = '';
  String _description = '';
  String? typechoisi = 'Autres';
  List<String> _type = ['Soiree', 'Journee', 'Autres'];
  List<int> check = [1];
  List<String> Promo = [];
  String? lieuchoisi;
  String Error_msg = '';

  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  String date = 'Sélectionner date';

  @override
  Widget build(BuildContext context) => SafeArea(
        child: buildPages(),
      );

  Widget buildPages() {
    return Scaffold(
      floatingActionButtonLocation: FloatingActionButtonLocation.startTop,
      floatingActionButton: Card(
        elevation: 10,
        margin: EdgeInsets.all(10),
        shadowColor: Colors.black,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(35)),
        child: InkWell(
          borderRadius: BorderRadius.circular(35),
          onTap: () {
            Navigator.pop(context);
          },
          child: SvgPicture.asset('asset/Image/arrow_back.svg'),
        ),
      ),
      backgroundColor: NOIR_FOND,
      body: SingleChildScrollView(
        padding: EdgeInsets.symmetric(horizontal: 32),
        child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
          Form(
            key: _formKey,
            child: Column(
              children: [
                SizedBox(height: 40),
                new Container(
                    margin: EdgeInsets.only(top: 15),
                    alignment: Alignment.topLeft,
                    child: Text("Evénèment",
                        style: new TextStyle(
                          color: Colors.white,
                          fontSize: 30,
                        ))),
                SizedBox(height: 10),
                new Container(
                  alignment: Alignment.topLeft,
                  child: Text("Créer votre évènement",
                      style: new TextStyle(
                        color: Color.fromRGBO(158, 158, 165, 1),
                        fontSize: 16,
                      )),
                ),
                //place d'insertion de la photo
                SizedBox(height: 15),
                Row(
                  children: [
                    new Container(
                      width: MediaQuery.of(context).size.width / 4,
                      height: MediaQuery.of(context).size.height / 10,
                      child: _image == null
                          ? Icon(
                              Icons.add_a_photo_outlined,
                              color: Colors.white,
                            )
                          : Image.file(_image!),
                    ),
                    TextButton(
                      onPressed: () {
                        getImage();
                        
                      },
                      child: new Container(
                        alignment: Alignment.center,
                        width: 200,
                        height: 50,
                        decoration: BoxDecoration(
                            borderRadius: BorderRadius.all(Radius.circular(5)),
                            color: Color.fromRGBO(255, 92, 57, 1)),
                        child: Text('Ajouter une photo',
                            textAlign: TextAlign.center,
                            style: new TextStyle(
                              fontSize: 16,
                              color: Colors.white,
                            )),
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 20),
                new Container(
                  alignment: Alignment.topLeft,
                  child: Text("Nom de l'évènement",
                      style: new TextStyle(
                        color: Color.fromRGBO(158, 158, 165, 1),
                        fontSize: 14,
                      )),
                ),
                new TextFormField(
                  onChanged: (value) => setState(() => _nom = value),
                  validator: (value) =>
                      value!.isEmpty ? 'Veuillez entrer un nom d\'évènement' : null,
                  style: TextStyle(color: Color.fromRGBO(255, 255, 255, 1)),
                  decoration: InputDecoration(
                    isDense: true,
                    filled: true,
                    fillColor: Color.fromRGBO(58, 58, 60, 1),
                    hintText: "Nom de l'évènement",
                    hintStyle: TextStyle(
                      color: Color.fromRGBO(158, 158, 165, 1),
                      fontSize: 16,
                    ),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(5),
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

                SizedBox(height: 20),
                new Container(
                  alignment: Alignment.topLeft,
                  child: Text("Description",
                      style: new TextStyle(
                        color: Color.fromRGBO(158, 158, 165, 1),
                        fontSize: 14,
                      )),
                ),
                new TextFormField(
                  onChanged: (value) => setState(() => _description = value),
                  validator: (value) => value!.isEmpty
                      ? 'Veuillez entrer une description'
                      : null,
                  style: TextStyle(color: Color.fromRGBO(255, 255, 255, 1)),
                  decoration: InputDecoration(
                    isDense: true,
                    filled: true,
                    fillColor: Color.fromRGBO(58, 58, 60, 1),
                    hintText: "Description",
                    hintStyle: TextStyle(
                      color: Color.fromRGBO(158, 158, 165, 1),
                      fontSize: 16,
                    ),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(5),
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
                SizedBox(height: 20),
                new Container(
                  alignment: Alignment.centerLeft,
                  child: Text("Choix du lieu de l'évènement",
                      style: new TextStyle(
                        color: Color.fromRGBO(158, 158, 165, 1),
                        fontSize: 14,
                      )),
                ),
                FutureBuilder<Info>(
                  future: futureInfo,
                  builder: (context, snapshot) {
                    if (snapshot.hasData) {
                      return Container(
                        alignment: Alignment.centerLeft,
                        padding: const EdgeInsets.only(left: 10.0, right: 10.0),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(5.0),
                          color: Color.fromRGBO(58, 58, 60, 1),
                        ),
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton(
                              isExpanded: true,
                              style: TextStyle(
                                color: Color.fromRGBO(158, 158, 165, 1),
                                fontSize: 16,
                              ),
                              hint: Text(
                                'Lieu',
                                style: TextStyle(
                                  color: Color.fromRGBO(158, 158, 165, 1),
                                  fontSize: 16,
                                ),
                              ),
                              dropdownColor: Color.fromRGBO(58, 58, 60, 1),
                              value: lieuchoisi,
                              onChanged: (String? tmp) {
                                setState(() {
                                  lieuchoisi = tmp;
                                });
                              },
                              items: List<DropdownMenuItem<String>>.generate(
                                  snapshot.data!.Lieux.length, (int compt) {
                                return DropdownMenuItem(
                                  child: new Text(
                                      snapshot.data!.Lieux[compt]['nom']),
                                  value: snapshot.data!.Lieux[compt]['@id'],
                                );
                              })),
                        ),
                      );
                    } else if (snapshot.hasError) {
                      return Text("${snapshot.error}");
                    }
                    return CircularProgressIndicator(color: NOIR_FOND);
                  },
                ),
                SizedBox(height: 20),
                new Container(
                  alignment: Alignment.centerLeft,
                  child: Text("Choix du type d'évènement",
                      style: new TextStyle(
                        color: Color.fromRGBO(158, 158, 165, 1),
                        fontSize: 14,
                      )),
                ),
                Container(
                  alignment: Alignment.centerLeft,
                  padding: const EdgeInsets.only(left: 10.0, right: 10.0),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(5.0),
                    color: Color.fromRGBO(58, 58, 60, 1),
                  ),
                  child: DropdownButtonHideUnderline(
                    child: DropdownButton(
                      isExpanded: true,
                      style: TextStyle(
                        color: Color.fromRGBO(158, 158, 165, 1),
                        fontSize: 16,
                      ),
                      dropdownColor: Color.fromRGBO(58, 58, 60, 1),
                      value: typechoisi == null ? 'Autres' : typechoisi,
                      onChanged: (String? tmp) {
                        setState(() {
                          typechoisi = tmp;
                        });
                      },
                      items: _type.map((promo) {
                        return DropdownMenuItem(
                          child: new Text(promo),
                          value: promo,
                        );
                      }).toList(),
                    ),
                  ),
                ),
                SizedBox(height: 20),
                Container(
                  alignment: Alignment.centerLeft,
                  child: Text("Promos concernées",
                      style: new TextStyle(
                        color: Color.fromRGBO(158, 158, 165, 1),
                        fontSize: 17,
                      )),
                ),
                FutureBuilder<Info>(
                  future: futureInfo,
                  builder: (context, snapshot) {
                    if (snapshot.hasData) {
                      return ListView.builder(
                          shrinkWrap: true,
                          primary: false,
                          physics: NeverScrollableScrollPhysics(),
                          itemCount: snapshot.data!.Promos.length,
                          itemBuilder: (BuildContext context, int compt) {
                            return Theme(
                                data: ThemeData(
                                    unselectedWidgetColor: Colors.white),
                                child: CheckboxListTile(
                                    activeColor: Colors.white,
                                    checkColor: Colors.black,
                                    title: Text(
                                      snapshot.data!.Promos[compt]['nom'],
                                      style: TextStyle(color: Colors.white),
                                    ),
                                    value: check.contains(
                                        snapshot.data!.Promos[compt]['id']),
                                    onChanged: (bool? value) {
                                      setState(() {
                                        if (value == true) {
                                          Promo.add(snapshot.data!.Promos[compt]
                                              ['@id']);
                                          check.add(snapshot.data!.Promos[compt]
                                              ['id']);
                                        } else {
                                          Promo.remove(snapshot
                                              .data!.Promos[compt]['@id']);
                                          check.remove(snapshot
                                              .data!.Promos[compt]['id']);
                                        }
                                      });
                                    }));
                          });
                    } else if (snapshot.hasError) {
                      return Text("${snapshot.error}");
                    }
                    return CircularProgressIndicator(color: NOIR_FOND);
                  },
                ),
                SizedBox(height: 10),
                new Container(
                  alignment: Alignment.center,
                  child: new TextButton(
                      onPressed: () {
                        if (_formKey.currentState!.validate() &&
                            lieuchoisi != null) {
                          Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) => Form_event2(
                                        typechoisi: typechoisi,
                                        promo: check,
                                        nom: _nom,
                                        nomAsso: nomAsso,
                                        description: _description,
                                        formKey: _formKey,
                                        image: _image,
                                        idpromo: Promo,
                                        lieu: lieuchoisi,
                                      )));
                        } else {
                          setState(() {
                            Error_msg = 'Completez les informations';
                          });
                          print(Error_msg);
                        }
                      },
                      child: new Container(
                        alignment: Alignment.center,
                        height: 50,
                        decoration: BoxDecoration(
                            borderRadius: BorderRadius.all(Radius.circular(4)),
                            color: Color.fromRGBO(255, 92, 57, 1)),
                        child: Text("Suivant",
                            textAlign: TextAlign.center,
                            style: new TextStyle(
                              fontSize: 16,
                              color: Colors.white,
                            )),
                      )),
                ),
                Text(Error_msg,
                    style:
                        TextStyle(color: Colors.red, height: 2, fontSize: 20)),
                SizedBox(height: 40),
              ],
            ),
          ),
        ]),
      ),
    );
  }
}
