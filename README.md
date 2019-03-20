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
# Name of your AWS EC2 SSH PrivateKey
user_sshkey: my-aws-key

# The private subnet where we will deploy this (VPC will be inherited from this)
user_subnet: subnet-12345678

# AWS KEY
user_awskey: AABBCCDDEEFFGGHH

# AWS Secret Key
user_awssec: JJKKLLMMNNOOPPQQRRSSTTUUVVWWXXYYZZ

# ID of your EC2 Security Group
user_secgrpid: sg-1234567890


# This is the title that will be tagged to keep track of these instances
user_event: Some ansible thing

# General vars, required by the role
user_region: us-east-1
user_realname: Kevin Holmes
user_email: kev@redhat.com

</pre>

## And a Tower key file called secret_tower_license.var

<pre>
license_file: |
  {
      "company_name": "RedHatROoT TurnKeyDemos", 
      "contact_email": "kev@redhat.com", 
      "contact_name": "Kevin Holmes", 
      "features": {
          "activity_streams": true, 
          "rebranding": true, 
          "surveys": true, 
          "system_tracking": true, 
          "workflows": true
      }, 
      "hostname": "abcdefghijklmnopqrstuvexyz1234567890", 
      "instance_count": 15, 
      "license_date": 1234567890,
      "license_key": "abcdefghijklmnopqrstuvexyz1234567890abcdefghijklmnopqrstuvexyz1234567890",
      "license_type": "basic", 
      "subscription_name": "RedHatROoT TurnKeyDemos", 
      "eula_accepted": true
  }

</pre>


## Example Playbook to build environment1 called build-environment1.yml:

<pre>
---
- name: TurnKeyDemo
  hosts: localhost
  gather_facts: no

#m5d.xlarge	16 GiB	4	1 x 150 NVMe SSD	0.23 USD

  vars:
    tower_inventory: ec2towertemp
    user_srvname: "kev-{{ user_event | replace (' ','') }}"
    user_instype: i3.large
    user_image: ami-0c170ccfc104032e3
    user_tags:
      Name: "{{ user_srvname }}"
      Event: "{{ user_event }}"
      Owner: "{{ user_realname }}"
      Contact: "{{ user_email }}"
      Application: "TurnKeyDemo for {{ user_event }}"

  roles:
     - role: ec2-deploy-instance


- hosts: ec2towertemp
  become: yes
  vars:
    ansible_ssh_user: centos
    ansible_ssh_private_key_file: /root/.ssh/kev.pem
    tower_user: kev
    tower_pass: kev
    tower_email: root@redhat.com

  roles:
    - role: tower-config
    - role: shutdown-time
      vars:
        system_shutdown: 720
        # 720 = 12 hours, # 1440 min = 1 day, 2160 = 1.5 days, 2880 = 2 days

</pre>


## Troubleshooting & Improvements

- Not enough testing yet

## Notes

  - Not enough testing yet

## Author

This project was created in 2018 by [Kevin Holmes](http://GoKEV.com/).


