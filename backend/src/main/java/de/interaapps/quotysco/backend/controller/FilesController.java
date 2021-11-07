package de.interaapps.quotysco.backend.controller;

import de.interaapps.quotysco.backend.QuotyscoBackend;
import de.interaapps.quotysco.backend.model.Session;
import de.interaapps.quotysco.backend.model.User;
import de.interaapps.quotysco.backend.responses.FileUploadedResponse;
import org.apache.commons.lang3.RandomStringUtils;
import org.apache.commons.net.ftp.FTP;
import org.javawebstack.framework.HttpController;
import org.javawebstack.httpserver.Exchange;
import org.javawebstack.httpserver.router.annotation.Attrib;
import org.javawebstack.httpserver.router.annotation.PathPrefix;
import org.javawebstack.httpserver.router.annotation.Post;
import org.javawebstack.httpserver.router.annotation.With;

import javax.servlet.ServletException;
import java.io.IOException;

@PathPrefix("/api/v1/files")
public class FilesController extends HttpController {
    @Post("/upload_image")
    @With("auth")
    public FileUploadedResponse editPicture(Exchange exchange, @Attrib("user") User user, @Attrib("session") Session session) {
        FileUploadedResponse response = new FileUploadedResponse();

        if (session.checkPermission("files:write")) {
            response.url = uploadFile(exchange, "posts/"+user.id+"_"+ RandomStringUtils.random(50, "abcdefghijklmnopqrstuvwxyz0123456789") +".png");
            response.success = true;
        }

        return response;
    }

    public static String uploadFile(Exchange exchange, String fileName){
        try {
            exchange.enableMultipart("file", 6_194_304);
            QuotyscoBackend.getInstance().getResourcesServer().setFileType(FTP.BINARY_FILE_TYPE);

            System.out.println(QuotyscoBackend.getInstance().getConfig().get("ftp.url")+QuotyscoBackend.getInstance().getConfig().get("ftp.basedir")+"/"+fileName);
            System.out.println(fileName);

            if (QuotyscoBackend.getInstance().getResourcesServer().storeFile(fileName, exchange.rawRequest().getPart("file").getInputStream())) {
                return QuotyscoBackend.getInstance().getConfig().get("ftp.url")+QuotyscoBackend.getInstance().getConfig().get("ftp.basedir")+"/"+fileName;
            } else {

                System.out.println(QuotyscoBackend.getInstance().getResourcesServer().getStatus());
            }
        } catch (IOException | ServletException e) {
            e.printStackTrace();
        }
        return "";
    }

}
