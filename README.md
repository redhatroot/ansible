[![RedHatROoT/ansible](https://github.com/redhatroot/ansible/blob/master/RedHatRoot.png)](https://github.com/redhatroot/ansible/)

<div style="position: absolute; top: 40px; left: 200px;">

# RedHatROoT Demo Environments

These are preconfigured projects that collectively share the roles in the directory.


## Here's an example of how you could launch this role:
<pre>
ansible-playbook -e @secrets.yml playbook.yml
</pre>

## With a secrets file called secrets.yml that looks like this:
<pre>
user_sshkey: my-ec2-keyname
user_subnet: subnet-01234567
user_awskey: ABCDEFGHIJKLMNOPQRST
user_awssec: abcdefghijklmnopqrstuvwxyz1234567890
user_region: us-east-1
user_event: Event Name or Customer
user_realname: Kevin Holmes
user_email: kev@redhat.com

</pre>


## Example Playbook to build environment1 called build-environment1.yml:

<pre>
---
- name: TurnKeyDemo
  hosts: localhost
  gather_facts: no

  vars:
    tower_inventory: ec2towertemp

  roles:
     - role: ec2-deploy-instance
       vars:
         user_srvname: "kev-{{ user_event | replace (' ','') }}"
         user_instype: t2.medium
         user_image: ami-0c170ccfc104032e3
         user_tags:
           Name: "{{ user_srvname }}"
           Event: "{{ user_event }}"
           Owner: "{{ user_realname }}"
           Contact: "{{ user_email }}"
           Application: "TurnKeyDemo for {{ user_event }}"

- hosts: ec2towertemp
  become: yes
  vars:
    ansible_ssh_user: centos
    ansible_ssh_private_key_file: /root/.ssh/kev-ansible-kev.pem
    tower_user: admin
    tower_pass: password
    tower_email: root@redhat.com
    tower_shutdown: 600

  roles:
    - tower-config

</pre>


## Troubleshooting & Improvements

- Not enough testing yet

## Notes

  - Not enough testing yet

## Author

This project was created in 2018 by [Kevin Holmes](http://GoKEV.com/).


