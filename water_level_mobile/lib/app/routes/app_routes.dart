part of 'app_pages.dart';

abstract class Routes {
  Routes._();
  static const HOME = _Paths.HOME;
  static const SETTINGS = _Paths.SETTINGS;
  static const DEVICE_MAP = _Paths.DEVICE_MAP;
  static const SPLASH = _Paths.SPLASH;
  static const LOGIN = _Paths.LOGIN;
  static const REGISTER = _Paths.REGISTER;
  static const FORGOT_PASSWORD = _Paths.FORGOT_PASSWORD;
  static const RESET_PASSWORD = _Paths.RESET_PASSWORD;
  static const ANALYSIS = _Paths.ANALYSIS;
  static const HISTORY_LOG = _Paths.HISTORY_LOG;
  static const COMPLETE_PROFILE = _Paths.COMPLETE_PROFILE;
  static const EDIT_PROFILE = _Paths.EDIT_PROFILE;
}

abstract class _Paths {
  _Paths._();
  static const HOME = '/home';
  static const SETTINGS = '/settings';
  static const DEVICE_MAP = '/device-map';
  static const SPLASH = '/splash';
  static const LOGIN = '/login';
  static const REGISTER = '/register';
  static const FORGOT_PASSWORD = '/forgot-password';
  static const RESET_PASSWORD = '/reset-password';
  static const ANALYSIS = '/analysis';
  static const HISTORY_LOG = '/history-log';
  static const COMPLETE_PROFILE = '/complete-profile';
  static const EDIT_PROFILE = '/edit-profile';
}
