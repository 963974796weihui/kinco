#!/bin/bash
filename=$1
operation=$2
value=$3
current_path="/root/openvpn_docker/release_1/deploy_map_related/openvpn/server/pf_dir/"
echo "filename=$filename"
echo "operation=$operation"
echo "value=$value"
echo "current_path=$current_path"

cd "$current_path"
     
if [ ! -s ""$current_path"$filename.pf" ]
then
     echo "file is not exist! Ready to create file..."
     touch $filename.pf
     chmod 777 $filename.pf
     echo "[CLIENTS DROP]" >> "$current_path"$filename.pf
     echo "[SUBNETS ACCEPT]" >> "$current_path"$filename.pf
     echo "[END]" >> "$current_path"$filename.pf
fi

sed -i '/-'"$value"'/d' "$current_path"$filename.pf
sed -i '/+'"$value"'/d' "$current_path"$filename.pf

if [ "$operation" = "-add" ]
then
     echo "ready to add..."
     sed -i '1a+'"$value"'' "$current_path"$filename.pf
elif [ "$operation" = "-del" ]
then
     echo "ready to delete..."
#     sed -i '1a-'"$value"'' "$current_path"$filename.pf
     sed -i '/+'"$value"'/d' "$current_path"$filename.pf
fi
