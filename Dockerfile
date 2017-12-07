FROM centos:latest

MAINTAINER evan suxw8813@qq.com

RUN yum -y install sudo subversion git

RUN echo "zentao ALL=NOPASSWD: ALL" >> /etc/sudoers

RUN useradd zentao

COPY .gitconfig /home/zentao/.gitconfig
RUN chown zentao:zentao /home/zentao/.gitconfig

EXPOSE 80

RUN mkdir -p /opt/zbox

COPY ./source/ /opt/zbox
COPY ./custom/ /opt/zbox

USER zentao

CMD ["/opt/zbox/zbox", "start"]
