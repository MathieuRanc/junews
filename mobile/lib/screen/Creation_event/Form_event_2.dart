import 'dart:io';

import 'package:flutter_svg/flutter_svg.dart';
import 'package:junews/Common/button_widget.dart';
import 'package:junews/Common/datetime_picker_widget.dart';
import 'package:flutter/material.dart';
import 'package:table_calendar/table_calendar.dart';
import 'package:junews/utils.dart';
import 'package:junews/Common/NavBar.dart';
import 'package:junews/Common/colors.dart';
import 'dart:convert';
import 'package:junews/Common/Global_variable.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;

Future Post_evenement(
    String Nom_event,
    String Desc_event,
    String Date_deb_event,
    String Date_fin_event,
    String Lieu,
    List<String> Promo,
    String Nom_asso,
    int id_photo,
    BuildContext context) async {
  await dotenv.load(fileName: ".env");
  final response = await http.post(
    Uri.parse(dotenv.env['BASE_URL_API']! + "/evenements"),
    body: json.encode({
      "nom": Nom_event,
      "description": Desc_event,
      "dateDebut": Date_deb_event,
      "dateFin": Date_fin_event,
      "promos": Promo,
      "lieu": Lieu,
      "association": Nom_asso,
      "image": id_photo,
    }),
    headers: {
      "accept": "application/ld+json",
      "Content-Type": "application/ld+json",
      "Authorization": "Bearer " + token,
    },
  );

  if (response.statusCode == 200 || response.statusCode == 201) {
    Navigator.pushAndRemoveUntil(
        context, MaterialPageRoute(builder: (_) => NavBAr()), (route) => false);
  } else {
    throw Exception('Failed to Load Evenement');
  }
}

Future Upload_image(File image) async {
  await dotenv.load(fileName: ".env");
  final request = http.MultipartRequest(
    'POST',
    Uri.parse(dotenv.env['BASE_URL_API']! + "/media_objects"),
  );
  request.headers['Content-type'] = 'multipart/form-data';
  request.files.add(await http.MultipartFile.fromPath('file', image.path));

  var response = await request.send();
  final res = await http.Response.fromStream(response);
  if (res.statusCode == 200 || res.statusCode == 201) {
    var resp = json.decode(res.body);
    idphoto = resp['id'];
  } else {
    throw Exception('Failed to Load image');
  }
}

class Form_event2 extends StatefulWidget {
  final image,
      nom,
      description,
      nomAsso,
      formKey,
      promo,
      typechoisi,
      idpromo,
      lieu;
  Form_event2(
      {Key? key,
      @required this.nomAsso,
      @required this.image,
      @required this.nom,
      @required this.description,
      @required this.formKey,
      @required this.promo,
      @required this.typechoisi,
      @required this.idpromo,
      @required this.lieu})
      : super(key: key);
  @override
  _Form_event2 createState() => _Form_event2(nomAsso, image, nom, description,
      formKey, promo, typechoisi, idpromo, lieu);
}

class _Form_event2 extends State<Form_event2> {
  String nomAsso, _description, _nom, typechoisi, lieu;
  File? _image;
  GlobalKey<FormState> _formKey;
  List<int> promo;
  List<String> idpromo;
  _Form_event2(this.nomAsso, this._image, this._nom, this._description,
      this._formKey, this.promo, this.typechoisi, this.idpromo, this.lieu);

  CalendarFormat _calendarFormat = CalendarFormat.month;
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay = DateTime.now();
  String date = 'Sélectionner une date';

  bool visibilityWidg = false;
  void _changed(bool visibility) {
    setState(() {
      visibilityWidg = visibility;
      if (visibility == false) {
        date = _selectedDay.toString().substring(0, 10);
      }
      ;
    });
  }

  Post_Calendar(String DateDebut, String DateFin, String lieuchoisi,
      List<int> promo) async {
    await dotenv.load(fileName: ".env");
    SharedPreferences sharedPreferences = await SharedPreferences.getInstance();
    final response = await http
        .post(Uri.parse(dotenv.env['BASE_URL_API']! + "/evenements/dispo"),
            body: json.encode({
              "dateDebutEvent": DateDebut,
              "dateFinEvent": DateFin,
              "typeEvent": lieuchoisi,
              "promotionEvent": promo
            }),
            headers: {
          "Accept": "application/ld+json",
          "Content-Type": "application/ld+json",
          "Authorization": "Bearer " + token,
        });
    if (response.statusCode == 200 || response.statusCode == 201) {
      var resp = json.decode(response.body);
      sharedPreferences.setString(
          "disponibilite", resp['disponibilite'].toString());
      setState(() {
        dispo = resp['disponibilite'];
      });
    } else {
      throw Exception('Failed to Load calendar');
    }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
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
      body: Center(
        child: SingleChildScrollView(
          padding: EdgeInsets.symmetric(horizontal: 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Container(
                alignment: Alignment.centerRight,
                child: Card(
                  color: Colors.transparent,
                  elevation: 10,
                  margin: EdgeInsets.all(10),
                  shadowColor: Colors.black,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(35)),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(35),
                    onTap: () => showDialog<String>(
                      context: context,
                      builder: (BuildContext context) => AlertDialog(
                        backgroundColor: NOIR_FOND,
                        title: const Text(
                          'Pourcentages des disponibilités :',
                          style: TextStyle(color: Colors.white),
                        ),
                        content: const Text(
                          'Dans le cas d’une « soirée » une personne sera considérée comme indisponible si elle est dans  une des promos concernée par l’évènement et qu’elle a un examen le lendemain.\n\nDans le cas d’une « journée » une personne sera  considérée  comme indisponible si elle est dans  une des promos concernée par l’évènement et qu’elle a au moins une minute de cours pendant l’évènement.\n\nDans le cas « Autres » il n’y a pas d’estimation de disponibilités.',
                          style: TextStyle(color: Color.fromRGBO(158, 158, 165, 1)),
                        ),
                        actions: <Widget>[
                          TextButton(
                            onPressed: () => Navigator.pop(context),
                            child: const Text('OK',
                                style: TextStyle(color: Colors.white)),
                          ),
                        ],
                      ),
                    ),
                    child: Icon(Icons.info_outline,color: Colors.white,),
                  ),
                ),
              ),
              TimePickerWidget(),
              SizedBox(height: 10),
              HeaderWidget(
                title: 'Choix de la date du debut de l\'évènement',
                child: visibilityWidg
                    ? Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.all(Radius.circular(4)),
                          color: Colors.white,
                        ),
                        child: Column(
                          children: [
                            TableCalendar(
                              firstDay: kFirstDay,
                              lastDay: kLastDay,
                              focusedDay: _focusedDay,
                              calendarFormat: _calendarFormat,
                              selectedDayPredicate: (day) {
                                // Use `selectedDayPredicate` to determine which day is currently selected.
                                // If this returns true, then `day` will be marked as selected.

                                // Using `isSameDay` is recommended to disregard
                                // the time-part of compared DateTime objects.
                                return isSameDay(_selectedDay, day);
                              },
                              onDaySelected: (selectedDay, focusedDay) {
                                if (!isSameDay(_selectedDay, selectedDay)) {
                                  // Call `setState()` when updating the selected day
                                  setState(() {
                                    _selectedDay = selectedDay;
                                    _focusedDay = focusedDay;
                                  });
                                }
                                var date_debut1 = DateTime(
                                        _focusedDay.year,
                                        _focusedDay.month,
                                        _focusedDay.day,
                                        time.hour,
                                        time.minute)
                                    .toString();
                                var date_fin1 = DateTime(
                                        _focusedDay.year,
                                        _focusedDay.month,
                                        _focusedDay.day,
                                        time_fin.hour,
                                        time_fin.minute)
                                    .toString();
                                date_debut1 =
                                    date_debut1.replaceAll(' ', 'T') + 'Z';
                                date_fin1 =
                                    date_fin1.replaceAll(' ', 'T') + 'Z';
                                Post_Calendar(
                                    date_debut1, date_fin1, typechoisi, promo);
                              },
                              onPageChanged: (focusedDay) {
                                // No need to call `setState()` here
                                _focusedDay = focusedDay;
                              },
                              startingDayOfWeek: StartingDayOfWeek.monday,
                              calendarBuilders: CalendarBuilders(
                                  defaultBuilder:
                                      (context, kFirstDay, kLastDay) =>
                                          Container(
                                            alignment: Alignment.center,
                                            margin: EdgeInsets.all(7),
                                            child: Text(
                                              kFirstDay.day.toString() + '\n',
                                              style: TextStyle(
                                                  color: Colors.black,
                                                  fontSize: 17,
                                                  fontWeight: FontWeight.bold,
                                                  height: 1.1),
                                            ),
                                          ),
                                  selectedBuilder:
                                      (context, kFirstDay, kLastDay) =>
                                          Container(
                                            alignment: Alignment.center,
                                            margin: EdgeInsets.all(5),
                                            decoration: BoxDecoration(
                                              color: ORANGE,
                                              shape: BoxShape.circle,
                                            ),
                                            child: Text(
                                              kFirstDay.day.toString() + '\n',
                                              style: TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 17,
                                                  fontWeight: FontWeight.bold,
                                                  height: 1.1),
                                            ),
                                          ),
                                  todayBuilder:
                                      (context, kFirstDay, kLastDay) =>
                                          Container(
                                            alignment: Alignment.center,
                                            margin: EdgeInsets.all(7),
                                            child: Text(
                                              kFirstDay.day.toString() + '\n',
                                              style: TextStyle(
                                                  color: Colors.black,
                                                  fontSize: 17,
                                                  fontWeight: FontWeight.bold,
                                                  height: 1.1),
                                            ),
                                          ),
                                  markerBuilder: (context, _focusedDay, oui) {
                                    if (_focusedDay != DateTime.now()) {
                                      return Positioned(
                                          top: 25,
                                          left: 13,
                                          child: Text(
                                              dispo[_focusedDay.day - 1],
                                              style: TextStyle(fontSize: 12)));
                                    }
                                  }),
                              calendarStyle: CalendarStyle(
                                // Use `CalendarStyle` to customize the UI
                                outsideDaysVisible: false,
                              ),
                              headerStyle: HeaderStyle(
                                titleTextStyle: TextStyle(
                                    fontWeight: FontWeight.bold, fontSize: 20),
                                titleCentered: true,
                                formatButtonVisible: false,
                              ),
                              locale: 'fr_FR',
                            ),
                            SizedBox(height: 20),
                            Container(
                              padding: EdgeInsets.symmetric(horizontal: 20),
                              height: 40,
                              decoration: BoxDecoration(
                                color: Colors.orange,
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
                                  visibilityWidg ? _changed(false) : null;
                                },
                                child: Text(
                                  'Valider',
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 16,
                                  ),
                                ),
                              ),
                            ),
                            SizedBox(height: 20)
                          ],
                        ),
                      )
                    : Container(
                        alignment: Alignment.center,
                        height: 40,
                        decoration: BoxDecoration(
                          color: ORANGE,
                          borderRadius: BorderRadius.all(Radius.circular(4)),
                        ),
                        child: TextButton(
                          onPressed: () {
                            visibilityWidg ? null : _changed(true);
                            Post_Calendar(
                                _focusedDay.toString().replaceAll(' ', 'T') +
                                    'Z',
                                DateTime(
                                            _focusedDay.year,
                                            _focusedDay.month,
                                            _focusedDay.day,
                                            time_fin.hour,
                                            time_fin.minute)
                                        .toString()
                                        .replaceAll(' ', 'T') +
                                    'Z',
                                typechoisi,
                                promo);
                          },
                          child: Text(
                            date,
                            style: TextStyle(fontSize: 16, color: Colors.white),
                          ),
                        ),
                      ),
              ),
              SizedBox(height: 40),
              new Container(
                alignment: Alignment.center,
                child: new TextButton(
                    onPressed: () async {
                      var date_debut = DateTime(
                              _focusedDay.year,
                              _focusedDay.month,
                              _focusedDay.day,
                              time.hour,
                              time.minute)
                          .toString();

                      var date_fin = DateTime(
                              _focusedDay.year,
                              _focusedDay.month,
                              _focusedDay.day,
                              time_fin.hour,
                              time_fin.minute)
                          .toString();

                      if (_formKey.currentState!.validate()) {
                        await Upload_image(_image!);
                        Post_evenement(_nom, _description, date_debut, date_fin,
                            lieu, idpromo, nomAsso, idphoto, context);
                      }
                    },
                    child: new Container(
                      alignment: Alignment.center,
                      height: 50,
                      decoration: BoxDecoration(
                          borderRadius: BorderRadius.all(Radius.circular(4)),
                          color: Color.fromRGBO(255, 92, 57, 1)),
                      child: Text("Créer l'évènement ",
                          textAlign: TextAlign.center,
                          style: new TextStyle(
                            fontSize: 16,
                            color: Colors.white,
                          )),
                    )),
              ),
            ],
          ),
        ),
      ));
}
