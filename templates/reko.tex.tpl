\documentclass[a4paper,12pt]{article}
\usepackage{german}
\usepackage[T1]{fontenc}
\usepackage[utf8]{inputenc}
\usepackage{color}
\usepackage[a4paper,vmargin={20mm,20mm},hmargin={20mm,10mm}]{geometry}
\usepackage{graphicx} 
\usepackage[absolute]{textpos}
\usepackage{ifthen}
\usepackage{textcomp}
\usepackage[official]{eurosym}
\usepackage{multirow}
\renewcommand\sfdefault{phv}
%\renewcommand\familydefault{\sfdefault}
\renewcommand{\familydefault}{\sfdefault}
\begin{document}
\input{bdzbrko2}
\briefkopf
\setlength{\parindent}{0mm}
\begin{center}
{\huge Reisekostenabrechnung}\\
{\large Bund Deutscher Zupfmusiker e.V.} \\
\end{center}

%\input{corikabrko}
\section{Persönliche Angaben}
\begin{tabular}[t]{ll}
\textbf{Name:} & <%$reko_data["name"]%> \\
\textbf{Straße:} & <%$reko_data["strasse"] %> \\
\textbf{PLZ/Ort:} & <%$reko_data["ort"] %> \\
\textbf{Funktion im BDZ} &  <%$reko_data["fkt"] %> \\
\\
\textbf{IBAN:} & <%$reko_data["iban"] %> \\
\textbf{BIC:} & <%$reko_data["bic"] %> \\
\textbf{Name der Bank:} & <%$reko_data["bank"] %> \\
\end{tabular}
\section{Reisedetails}
\begin{tabular}[t]{ll}
\textbf{Reiseroute} & <%$reko_data["route"] %> \\
\textbf{Beginn der Reise} & <%$reko_data["beginn"] %>, <%$reko_data["beginnZeit"] %> \\
\textbf{Ende der Reise} & <%$reko_data["ende"] %>, <%$reko_data["endeZeit"] %> \\
\textbf{Zweck der Reise} & <%$reko_data["grund"] %> \\
\end{tabular}
\section{Entstandene Kosten}
\begin{tabular}[t]{lrlr}

<%foreach from=$reko_data["kosten"] item=cost_row %>
<%$cost_row["descr"] %> & <%$cost_row["unit"] %> & <%$cost_row["each"] %> & <%$cost_row["sum"] %>\\
<%/foreach %>
\hline
\textbf{Summe} & & & \textbf{ <%$reko_data["master_sum"] %> } \\
\hline
\hline
\end{tabular} \\
\vspace{1cm}

<%$reko_data["abweichungen"] %>

\vfill
\begin{tabular}{lp{4em}l}
\hspace{7cm}   && \hspace{7cm} \\
\cline{1-1}\cline{3-3}
 Ort, Datum     && Unterschrift
\end{tabular} 
\end{document}
