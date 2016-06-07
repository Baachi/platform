Attendees
=========

- entity bound to calendar event which represents one attendee of the event
- can be optionally bound to some user in crm
- contains reference to parent calendar event
- each calendar event have reference to related attendee so that we know which attendee owns related calendar
- when changes related to attendees are made via api (added attendees, removed attendees, event changes...)
  attendees are not notified about them unless send_notification=true parameter is provided in request url
