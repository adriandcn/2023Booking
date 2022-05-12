/*!
 * Availability Booking Calendar v4.0
 * http://www.phpjabbers.com/availability-booking-calendar/
 * 
 * Copyright 2013, StivaSoft Ltd.
 * http://www.phpjabbers.com/license-agreement.php
 * http://www.phpjabbers.com/licence-explained.php
 * 
 * Date: Fri Jan 04 10:37:01 2013 +0200
 */
(function (window, undefined){
	"use strict";
	var document = window.document;
	
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	
	function ABCalendarAvailability(opts) {
		if (!(this instanceof ABCalendarAvailability)) {
			return new ABCalendarAvailability(opts);
		}
		this.reset.call(this);
		this.init.call(this, opts);
		return this;
	}
	
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),

	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	};
	
	ABCalendarAvailability.prototype = {
		reset: function () {
			this.opts = null;
			this.$abWrapper = null;
			var dt = new Date();
			this.month = dt.getMonth() + 1;
			this.year = dt.getFullYear();
			
			return this;
		},
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.$abAvailability = pjQ.$("#abAvailability_" + this.opts.index);
			
			this.getAvailability.call(this, this.opts.year, this.opts.month);

			// Event delegation
			this.$abAvailability.on("click.ab", ".abCal-link", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var cid = pjQ.$(this).data("id"),
					load_msg = pjQ.$('.abErrorMessage').data('msg');
				function loadCalendar() {
					loadRemote([self.opts.folder, "index.php?controller=pjFront&action=pjActionLoadCss&cid=", cid].join(""), "css");
					pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLoadCalendar&cid=", cid, "&locale=", self.opts.locale, "&session_id=", self.opts.session_id].join("")).done(function (data) {
						self.$abAvailability.replaceWith(data.template);
						if (ABCalendar !== undefined) {
							var abName = "ABCalendar_" + cid,
							options = {
								server: self.opts.server,
								folder: self.opts.folder,
								session_id: self.opts.session_id,
								cid: cid,
								view: 1,
								locale: self.opts.locale,
								index: self.opts.index,
								year: self.year,
								month: self.month,
	
								booking_behavior: parseInt(data.opts.o_booking_behavior, 10),
								price_based_on: data.opts.o_price_based_on,
								price_plugin: data.opts.o_price_plugin,
								accept_bookings: parseInt(data.opts.o_accept_bookings, 10),
								show_prices: parseInt(data.opts.o_show_prices, 10),
								week_start: parseInt(data.opts.o_week_start, 10),
								date_format: data.opts.o_date_format,
								thankyou_page: data.opts.o_thankyou_page,
								limits: data.limits,
								days: data.days,
								error_msg: data.error_msg,
								load_msg: load_msg
							};
							window[abName] = new ABCalendar(options);
						}
					});
				}
				
				if (window.ABCalendar !== undefined) {
					loadCalendar();
				} else {
					loadRemote(self.opts.server + "core/libs/pjQ/pjQuery.validate.min.js", "js", function () {
						loadRemote(self.opts.server + "app/web/js/pjABCalendar.js", "js", function () {
							loadCalendar();
						});
					});
				}
				
				return false;
			}).on("click.ab", ".abCal-prev-month, .abCal-next-month", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this);
				self.getAvailability.call(self, $this.data("year"), $this.data("month"));
				return false;
			}).on("click.ab", ".abSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this);
				$this.addClass("abLocaleFocus").parent().parent().find("a.abSelectorLocale").not(this).removeClass("abLocaleFocus");
				
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale"].join(""), {
					"locale_id": $this.data("id"),
					"session_id": self.opts.session_id
				}).done(function (data) {
					self.getAvailability.call(self);
				});
				return false;
			});
		},
		getAvailability: function (year, month) {
			var self = this;
			if (year) {
				this.year = year;
			}
			if (month) {
				this.month = month;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetAvailability"].join(""), {
				"year": this.year,
				"month": this.month,
				"session_id": this.opts.session_id,
				"locale": this.opts.locale
			}).done(function (data) {
				self.$abAvailability.html(data);
				var title_height = null;
				self.$abAvailability.find('.abCal-container > .abCal-calendars > .abCal-title').each(function(e){
					if(title_height == null)
					{
						title_height = pjQ.$(this).find('.abCal-note').height();
						if(title_height > 64)
						{
							pjQ.$(this).css("height", "76px");
							self.$abAvailability.find('.abCal-container > .abCal-dates > .abCal-scroll > .abCal-head').css("height", "76px");
						}
					}
				});
			});
		}
	};
	
	// expose
	window.ABCalendarAvailability = ABCalendarAvailability;
})(window);