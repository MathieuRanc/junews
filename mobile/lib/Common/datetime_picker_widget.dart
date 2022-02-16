import 'package:junews/Common/button_widget.dart';
import 'package:flutter/material.dart';

TimeOfDay time = TimeOfDay.now();
TimeOfDay time_fin = TimeOfDay.now();

class TimePickerWidget extends StatefulWidget {
  @override
  _TimePickerWidgetState createState() => _TimePickerWidgetState();
}

class _TimePickerWidgetState extends State<TimePickerWidget> {
  String getText() {
    final hours = time.hour.toString().padLeft(2, '0');
    final minutes = time.minute.toString().padLeft(2, '0');
    final hours_fin = time_fin.hour.toString().padLeft(2, '0');
    final minutes_fin = time_fin.minute.toString().padLeft(2, '0');
    return '$hours:$minutes / $hours_fin:$minutes_fin';
  }

  @override
  Widget build(BuildContext context) => ButtonHeaderWidget(
        title: 'Choix de l\'heure de début et de fin',
        text: getText(),
        onClicked: () => pickTime(context),
      );

  Future pickTime(BuildContext context) async {
    final newTime = await showTimePicker(
      helpText: 'HEURE DE DEBUT',
      context: context,
      initialTime: time,
    );
    setState(() {
      newTime != null ? time = newTime : null;
    });
    final newTime2 = await showTimePicker(
      helpText: 'HEURE DE FIN',
      context: context,
      initialTime: time,
    );
    setState(() {
      newTime2 != null ? time_fin = newTime2 : time_fin = time;
    });
  }
}

buildtheme() {
  final ThemeData base = ThemeData.dark();
  return base.copyWith(textTheme: TextTheme());
}
