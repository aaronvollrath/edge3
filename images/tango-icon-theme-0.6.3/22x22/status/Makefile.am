
size = 22x22
context = status

iconsdir = $(themedir)/$(size)/$(context)

icons_DATA =				\
	battery-caution.png		\
	dialog-error.png		\
	dialog-information.png		\
	dialog-warning.png		\
	folder-drag-accept.png		\
	folder-open.png			\
	folder-visiting.png		\
	image-loading.png		\
	image-missing.png		\
	locked.png			\
	mail-attachment.png		\
	network-wireless-encrypted.png	\
	unlocked.png			\
	user-trash-full.png

EXTRA_DIST =				\
	$(icons_DATA)

install-data-local: install-iconsDATA
	(cd $(DESTDIR)$(themedir)/$(size) && $(ICONMAP) -c $(context))
if ENABLE_ICON_FRAMING
	$(mkinstalldirs) $(DESTDIR)$(themedir)/24x24/$(context)
	(cd $(DESTDIR)$(themedir) && \
	for icon in $(icons_DATA); do \
		$(CONVERT) $(size)/$(context)/$$icon 24x24/$(context)/$$icon; \
	done)
	(cd $(DESTDIR)$(themedir)/24x24 && $(ICONMAP) -c $(context))
endif

uninstall-local:
	rm -rf $(themedir)/24x24/$(context)
