# Added for Debian GNU/Linux
DESTDIR =

BINDIR = $(DESTDIR)/usr/sbin
CFLAGS = -O2
CC=gcc
INCLUDE = -I/usr/kerberos/include -I/usr/kerberos/include/krb5
KRB5_LIBS_DIR = -L/usr/kerberos/lib
KRB5_BASE_LIBS = -lkrb5 -lk5crypto -lcom_err

kpoppassd: kpoppassd.c Makefile
	$(CC) $(CFLAGS) kpoppassd.c -o kpoppassd -I$(INCLUDE) $(INCLUDE) $(KRB5_LIBS_DIR) $(KRB5_BASE_LIBS) $(FLAGS)

install: kpoppassd
	install -d $(BINDIR)
	install -m755 kpoppassd $(BINDIR)

clean:
	rm -f *.o *~* core Makefile.new Makefile.bak kpoppassd
