# php-teleduino328-client
PHP Teleduino Client for interacting with your Teleduino328 device.

## Change Log

0.3.3 (2014-02-16)
- Added support for saving setDigitalOutput as preset value

0.3.2 (2013-09-11)
- Added method loadPresets
- Added method setDigitalOutputs

0.3.1 (2012-07-28)
- Bug fix to ensure setDigitalOutput remains backward compatible

0.3.0 (2012-07-28)
- Added support for expire time on digital outputs
- Changed proxy address structure to support SSL

0.2.2 (2012-05-15)
- Removed UTF-8 encoding from previous version. The conversion belongs
  elsewhere!
- Fixed bug with get/set preset values for the Wire preset

0.2.1 (2012-05-14)
- Added UTF-8 encoding to getEeprom and getSerial methods so that non-visible
  characters don't cause problems when being displayed as plain text

0.2.0 (2012-05-10)
- Changed config structure to allow for future expandability
- Changed curl methods to allow for future expandability
- Added method getServo
- Added Wire methods:
  - defineWire
  - setWire
  - getWire
- Added example code for Wire

0.1.0 (2012-04-02)
- Initial public release
