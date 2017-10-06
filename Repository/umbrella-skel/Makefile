all: skel.tar.gz

skel.tar.gz: $(shell find skel -type f)
	cd skel/ && tar cvfz ../skel.tar.gz ./ && cd ..

clean:
	-rm -f skel.tar.gz
