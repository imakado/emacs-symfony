;;; symfony.el -- minor mode for editting PHP symfony flamework code.

;; Copyright (c) 2009 by KAYAC Inc.

;; Author: IMAKADO <ken.imakado -at-  gmail.com>
;; blog: http://d.hatena.ne.jp/IMAKADO (japanese)
;; Prefix: sf:

;; This file is free software; you can redistribute it and/or modify
;; it under the terms of the GNU General Public License as published by
;; the Free Software Foundation; either version 2, or (at your option)
;; any later version.

;; This file is distributed in the hope that it will be useful,
;; but WITHOUT ANY WARRANTY; without even the implied warranty of
;; MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
;; GNU General Public License for more details.

;; You should have received a copy of the GNU General Public License
;; along with GNU Emacs; see the file COPYING.  If not, write to the
;; Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
;; Boston, MA 02110-1301, USA.


;;; Commentary:

;; tested only on emamcs22

;; some code taken from rails.el


;; To run test,

;; git clone git@github.com:imakado/emacs-symfony.git ~/dev/emacs-symfony
;; cd ~/dev/emacs-symfony
;; ./run-test.sh
;; => 0 failures, 0 errors (all tests successful)


;;; Installation:

;; install requires libraries:
;; `anything.el' http://www.emacswiki.org/emacs/anything.el
;; `anything-match-plugin.el'  http://www.emacswiki.org/emacs/anything-match-plugin.el
;; `anything-project.el' http://github.com/imakado/emacs-symfony/tree/master
;; `anything-grep'  http://www.emacswiki.org/emacs/anything-grep.el

;; `symfony.el' http://github.com/imakado/emacs-symfony/tree/master (this file)

;; add these lines to your .emacs file:
;; (require 'symfony)


;;; TODO:
;; - Integration with symfony command.
;; - Code Completion

(require 'cl)
(require 'rx)
(require 'etags)
(require 'php-mode)

(require 'anything)
(require 'anything-match-plugin)
(require 'anything-project)
(require 'anything-grep)
(require 'php-completion)


;; i'm not sure, anyone can change this? - IMAKADO
(defconst sf:MODULES-DIR-NAME "modules")
(defconst sf:TEMPLATES-DIR-NAME "templates")
(defconst sf:APP-MODULE-ACTION-DIR-NAME "actions")
(defconst sf:ACTIONS-CLASS-PHP "actions.class.php")
(defconst sf:ACTIONS-FILE-RULE "Action.class.php")

(defvar sf:mode-directory-rules
  `(
    (action ,(rx-to-string `(and  "apps/" (+ not-newline) "/" ,sf:APP-MODULE-ACTION-DIR-NAME)))
    (template ,(rx-to-string `(and  "apps/" (+ not-newline) "/" ,sf:TEMPLATES-DIR-NAME)))
    ))

(defvar sf:primary-switch-fn 'sf-cmd:all-project-files
  "minor mode overide this")
(make-variable-buffer-local 'sf:primary-switch-fn)

(defvar sf:after-anything-project-action-hook nil
  "list of functions called after anything project(symfony-mode's `find-file').
Note,this variable MUST BE let bounded in command.
e.x,
 (let ((sf:after-anything-project-action-hook
        (list
         (lambda () (re-search-forward \"function_name\" nil t)))))
   (sf:anything-project candidates))")

(defcustom sf:anything-project-exclude-regexps nil
  "list of regexp or just regexp")

(defvar sf:previous-log-file nil)

(defvar sf:number-of-lines-shown-when-opening-log-file 200)

(defmacro* sf:with-root (&body body)
  (let ((root (gensym)))
    `(let ((,root (sf:get-project-root)))
       (when ,root
         (flet ((sf:get-project-root () ,root))
           ,@body)))))

(eval-when-compile
  (def-edebug-spec sf:with-root (&optional body)))

(defmacro* sf:with-root-default-directory (&body body)
  `(sf:with-root
    (let ((default-directory (sf:get-project-root)))
      (progn ,@body))))

(eval-when-compile
  (def-edebug-spec sf:with-root-default-directory (&optional body)))

(defun sf:project-absolute-path (file-name)
  (sf:with-root
   (cond
    ((file-name-absolute-p file-name)
     file-name)
    (t
     (let ((root-dir (sf:get-project-root)))
       (if root-dir
           (sf:catdir root-dir file-name)
         ""))))))

(defun sf:buffer-type ()
  (loop for (type re-or-fn) in sf:mode-directory-rules
        when (if (stringp re-or-fn)
                 (string-match re-or-fn (sf:current-directory))
               (funcall re-or-fn))
        do (return type)))

(defun sf:current-directory ()
  (file-name-directory
   (expand-file-name
    (or (buffer-file-name)
        default-directory))))

(defun sf:this-file-name ()
  (unless buffer-file-name
    (error "this buffer maybe not saved!!"))
  (file-name-nondirectory buffer-file-truename))

;;; LIB
(defconst sf:find-upper-directory-limit 10)
(defvar sf:root-detector-fn
  (lambda (current-directory)
    (assert (file-directory-p current-directory))
    (let ((files (directory-files current-directory)))
      (let ((symfony-files '("apps" "config")))
        (every
         (lambda (file)
           (find file files :test 'string=))
         symfony-files)))))

(defun sf:get-project-root ()
  (let ((cur-dir (sf:current-directory)))
    (sf:find-upper-directiory sf:root-detector-fn)))

(defun sf:find-upper-directiory (cond-fn)
  (assert (functionp cond-fn))
  (let ((cur-dir (sf:current-directory)))
    (loop with count = 0
        until (funcall cond-fn cur-dir)
        if (= count sf:find-upper-directory-limit)
        do (return nil)
        else
        do (progn (incf count)
                  (setq cur-dir (expand-file-name (concat cur-dir "../"))))
        finally return cur-dir)))

(defun sf:take-function-name ()
  (ignore-errors
    (save-excursion
      (forward-line 1)
      (let ((fname-re (rx bol
                          (* space)
                          "public function"
                          (+ space)
                          (group
                           (+
                            (or (syntax word) (syntax symbol))))
                          "("
                          )))
        (when (re-search-backward fname-re nil t)
          (match-string-no-properties 1))))))

(defun sf:take-off-execute (s)
  (when (stringp s)
    (replace-regexp-in-string (rx bol "execute") "" s)))

(defun sf:take-off-action (s)
  (when (stringp s)
    (let ((case-fold-search nil))
      (replace-regexp-in-string (rx "Action" eol) "" s))))

(defun sf:take-off-tail-capital (s)
  (let ((case-fold-search nil))
  (when (stringp s)
    (when (string-match (rx bol (group (+ print)) (regexp "\\(?:[A-Z][a-z]+\\)") eol) s)
      (match-string 1 s)))))

(defun sf:catdir (s1 s2)
  (let ((s1 (replace-regexp-in-string (rx "/" eol) "" s1))
        (s2 (replace-regexp-in-string (rx bol "/") "" s2)))
    (concat s1 "/" s2)))

(defvar sf:project-cache nil)
(defun* sf:project-files (&optional clear-cache (include-regexps '(".*")) (exclude-regexps sf:anything-project-exclude-regexps))
  (setq clear-cache (or clear-cache current-prefix-arg))
  (sf:with-root
   (let ((root-dir (sf:get-project-root))
         (ap:--cache sf:project-cache)) ;; use own cache
     (unless root-dir
       (error "this buffer is not symfony project file"))
     (let ((ap:projects nil))
       (ap:add-project
        :name 'symfony
        :look-for sf:root-detector-fn
        :grep-extensions '("\\.php"))
       (when clear-cache
         (setq ap:--cache
               (delete-if (lambda (ls) (equal root-dir ls))
                          ap:--cache
                          :key 'car)))
       (lexical-let ((root-dir root-dir))
         (setq ap:root-directory root-dir)
         (ap:cache-get-or-set
          root-dir
          (lambda ()
            (message "getting project files...")
            (let ((include-regexp include-regexps)
                  (exclude-regexp exclude-regexps))
              (let* ((files (ap:directory-files-recursively
                             include-regexp
                             root-dir
                             'identity
                             exclude-regexp)))
                files)))))))))

(defun sf:abs->relative (los)
  (assert (listp los))
  (mapcar 'file-relative-name los))

(defun sf:get-module-dir-or-root ()
  (let ((cur-dir (sf:current-directory)))
    (cond
     ((string-match (rx (group bol (* not-newline) "apps" (+ not-newline) "modules" (? "/")))
                    cur-dir)
      (match-string 1 cur-dir))
     (t
      (sf:get-project-root)))))

(defun sf:get-templates-directory ()
  (let ((templates-finder
         (lambda (cur-dir)
           (let ((template-dir (sf:catdir cur-dir (concat "/" sf:TEMPLATES-DIR-NAME "/"))))
             (file-directory-p template-dir)))))
    (let ((ret (sf:find-upper-directiory templates-finder)))
      (when ret
        (list (sf:catdir ret sf:TEMPLATES-DIR-NAME))))))

(defun sf:get-templates-file-by-action-name (action-name)
  "return list of templates or nil
Note, dont return just STRING even if find one template file."
  (let ((files (sf:get-templates-directory)))
    files))

(defcustom sf:quickly-find-file-when-candidates-length-is-1 t
  "if this variable is set to non-nil and candidates is just one,
find file quickly (dont use anything interface)")

(defun sf:anything-project (--candidates)
  (cond
   ((and sf:quickly-find-file-when-candidates-length-is-1
         (= (length --candidates) 1))
    (sf:anything-project-find-file (first --candidates)))
   (t
    (let ((source
           `((name . ,(format "Project files. root: %s" (or (sf:get-project-root) "")))
             (init . (lambda ()
                       (with-current-buffer (anything-candidate-buffer 'local)
                         (insert (mapconcat 'identity --candidates "\n")))))
             (candidates-in-buffer)
             (action . (("Find file" .
                         sf:anything-project-find-file))))))
      (anything (list source))))))

(defun sf:anything-project-find-file (c)
  (find-file c)
  (ignore-errors (run-hooks 'sf:after-anything-project-action-hook)))

(defsubst sf:any-match (regexp-or-regexps file-name)
  (when regexp-or-regexps
    (let ((regexps (if (listp regexp-or-regexps) regexp-or-regexps (list regexp-or-regexps))))
      (some
       (lambda (re)
         (string-match re file-name))
       regexps))))

(defun* sf:directory-files-recursively (regexp &optional directory type (dir-filter-regexp nil) (exclude-regexps sf:anything-project-exclude-regexps))
  (let* ((directory (or directory default-directory))
         (predfunc (case type
                     (dir 'file-directory-p)
                     (file 'file-regular-p)
                     (otherwise 'identity)))
         (files (directory-files directory t "^[^.]" t))
         (files (mapcar 'ap:follow-symlink files))
         (files (remove-if (lambda (s) (string-match (rx bol (repeat 1 2 ".") eol) s)) files)))
    (loop for file in files
          when (and (funcall predfunc file)
                    (ap:any-match regexp (file-name-nondirectory file))
                    (not (ap:any-match exclude-regexps file)))
          collect file into ret
          when (and (file-directory-p file)
                    (not (ap:any-match dir-filter-regexp file)))
          nconc (ap:directory-files-recursively regexp file type dir-filter-regexp) into ret
          finally return  ret)))

(defun sf:get-module-directory ()
  "return string or nil"
  (let ((cur-dir (sf:current-directory)))
     (when (string-match (rx (group bol (* not-newline) "apps" (+ not-newline) "modules/" (+ (not (any "/"))) "/"))
                         cur-dir)
      (match-string 1 cur-dir))))

(defun sf:relative-files ()
  (let ((module-directory (sf:get-module-directory)))
    (cond
     ((and module-directory (file-directory-p module-directory))
      (sf:directory-files-recursively ".*" module-directory 'file-regular-p nil sf:anything-project-exclude-regexps))
     (t
      (sf:project-files)))))

(defun sf:matched-files (regexp)
    (let ((files (sf:project-files))
          (re (rx-to-string `(and  "/" ,regexp "/"))))
    (remove-if-not (lambda (s) (string-match re s))
                   files)))

(defun sf:get-log-directory ()
  (let ((root-dir (sf:get-project-root)))
  (when root-dir
    (let ((log-dir (sf:catdir root-dir "log/")))
      (when (and log-dir (file-accessible-directory-p log-dir))
        log-dir)))))

(defun sf:make-log-buffer-name (log-file)
  (concat "*" log-file "*"))


(defun sf:open-log-file (log-file)
  (let ((bufname (sf:make-log-buffer-name log-file)))
    (unless (get-buffer bufname)
      (get-buffer-create bufname)
      (set-buffer bufname)
      (setq auto-window-vscroll t)
      (symfony-minor-mode t)
      (start-process "symfony-tail"
                     bufname
                     "tail"
                     "-n" (format "%d" sf:number-of-lines-shown-when-opening-log-file )
                     "-f"
                     (expand-file-name log-file))
      (current-buffer)
      )))

(defsubst sf:trim (s)
  "strip space and newline"
  (replace-regexp-in-string
   "[ \t\n]*$" "" (replace-regexp-in-string "^[ \t\n]*" "" s)))

(defun sf:command-infomation ()
  (let ((re (rx bol
                (group (+ not-newline))
                (group "sf-cmd:"
                       (+ not-newline)
                       eol))))
    (with-temp-buffer
      (goto-char (point-min))
      (insert (substitute-command-keys (format "\\{%s}" "sf:minor-mode-map")))
      (loop initially (goto-char (point-min))
            while (re-search-forward re nil t)
            collect (let* ((key (sf:trim (match-string 1)))
                           (command (sf:trim (match-string 2)))
                           (display key)
                           (real command))
                      `(,display . ,real))))))

(defun sf:remove-if-not-match (re los)
  (remove-if-not (lambda (s) (string-match re s)) los))


(defun sf:get-application-names ()
  (let ((app-name-re (rx "apps/" (group (+ (not (any "/")))) "/")))
    (loop for path in (sf:project-files)
          when (string-match app-name-re path)
          collect (match-string 1 path) into ret
          finally return (delete-dups ret))))

;;;; Create Partial
(defun sf:create_partial_on_region (&optional start end)
  (interactive "r")
  (let ((str (buffer-substring-no-properties start end))
        (partial-file-name (read-file-name
                            "partial name: "
                            (car-safe (sf:get-templates-directory))
                            )))
    (when (and partial-file-name (not (string= partial-file-name "")))
      (delete-region start end)
      (insert (concat "<?php echo include_partial('"
                      (file-name-nondirectory (expand-file-name partial-file-name))
                      "') ?>"))
      (find-file partial-file-name)
      (goto-char (point-min))
      (insert str))))

;;;; Tags
(defvar sf:tags-dirs '("apps" "lib"))
(defvar sf:tags-command "ctags -e -a  -R --php-types=c+f+d+v+i -o %s --langmap=PHP:.php.inc   %s")
(defvar sf:tags-file-name "TAGS")
(defvar sf:tags-cache nil
  "list of structure `phpcmp-tag'")

(defun sf:make-create-tags-command ()
  (sf:with-root
   (let* ((tags-file-name (sf:project-absolute-path sf:tags-file-name))
          (command (format sf:tags-command sf:tags-file-name
                           (mapconcat 'identity (mapcar 'sf:project-absolute-path sf:tags-dirs) " "))))
     command)))

(defun sf:get-tags-file ()
  "return tags-file full path, when not exsist ask to generate"
  (let ((tags-file (sf:project-absolute-path sf:tags-file-name)))
    (cond
     (tags-file)
     (t
      (when (y-or-n-p "no TAGS file. generate? ")
        (sf-cmd:create-or-update-tags))))))

(defun sf:get-tags-structs ()
  "list of structure `phpcmp-tag'.
when sf:tags-cache is set, return it."
  (cond
   (sf:tags-cache)
   (t
    (let ((tags-file (sf:get-tags-file)))
      (cond
       ((null tags-file)
        (error "no TAGS file!!"))
       (t
        (setq sf:tags-cache
              (phpcmp-etags-get-tags tags-file))))))))

(defun sf:tags-build-class-candidates ()
  (let ((tags (sf:get-tags-structs)))
    (loop for tag in tags
          append (sf:tags-build-class-candidates-1
                  (phpcmp-tag-classes tag)
                  (phpcmp-tag-path tag)))))

(defun sf:tags-build-class-candidates-1 (classes file-path)
  (loop for class in classes
        append (loop for method in (phpcmp-class-methods class)
                     collect (cons
                              (concat (phpcmp-class-name class) " : " method) ;DISPLAY . REAL
                              file-path))))
;; (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
;;   (sf-cmd:create-or-update-tags)
;;   (sf-cmd:update-caches)
;;   (sf:tags-build-class-candidates))

;;;; Commands
;; Prefix: sf-cmd:
(defun sf-cmd:all-project-files ()
  (interactive)
  (sf:anything-project (sf:project-files)))

(defun sf-cmd:primary-switch ()
  (interactive)
  (funcall sf:primary-switch-fn))

(defun sf-cmd:relative-files ()
  (interactive)
  (sf:anything-project (sf:relative-files)))

(defun sf-cmd:model-files ()
  (interactive)
  (sf:anything-project (sf:matched-files "model")))

(defun sf-cmd:action-files ()
  (interactive)
  (sf:anything-project (sf:matched-files "actions")))

(defun sf-cmd:template-files ()
  (interactive)
  (sf:anything-project (sf:matched-files "templates")))

(defun sf-cmd:helper-files ()
  (interactive)
  (sf:anything-project (sf:matched-files "helper")))

(defun sf-cmd:test-files ()
  (interactive)
  (sf:anything-project (sf:matched-files "test")))

(defun sf-cmd:open-log-file (log-file)
  (interactive
   (list
    (expand-file-name
    (read-file-name (format "Select log[default: %s]: " sf:previous-log-file)
                    (sf:get-log-directory)
                    sf:previous-log-file
                    t
                    ))))
  (setq sf:previous-log-file log-file)
  (let ((log-buffer  (sf:open-log-file log-file)))
    (switch-to-buffer log-buffer)
    (recenter t)))

(defun sf-cmd:create-or-update-tags ()
  (interactive)
  (sf:with-root-default-directory
   (let ((command (sf:make-create-tags-command))
         (tags-file-path (sf:project-absolute-path sf:tags-file-name)))
     (when command
       (shell-command command)
       (flet ((yes-or-no-p (p) t))
         (visit-tags-table tags-file-path)
         )))))

(defun sf-cmd:update-caches ()
  (interactive)
  (setq sf:project-cache nil)
  (setq sf:tags-cache nil)
  (sf-cmd:create-or-update-tags))

;;;; Minor Mode
(defmacro sf:key-with-prefix (key-kbd-sym)
  (let ((key-str (symbol-value key-kbd-sym)))
    `(kbd ,(concat sf:minor-mode-prefix-key " " key-str))))

(defvar sf:minor-mode-map
  (make-sparse-keymap))

(define-minor-mode symfony-minor-mode
  "symfony minor mode"
  nil
  " symfony"
  sf:minor-mode-map)

(defun symfony-minor-mode-maybe ()
  (let ((root-dir (sf:get-project-root)))
    (if root-dir
        (symfony-minor-mode 1)
      (symfony-minor-mode 0))
    ;; specify minor mode on
    (when root-dir
      (let ((minor-mode-name (sf:get-specify-minor-mode-string)))
        (when minor-mode-name
          (funcall (intern minor-mode-name) t))))))

(defun sf:get-specify-minor-mode-string ()
  (let ((type (sf:buffer-type)))
    (when type
      (format "symfony-%s-minor-mode" type))))

(defcustom sf:minor-mode-prefix-key "C-c"
  "Key prefix for symfony minor mode."
  :group 'symfony)

(defun sf:define-key (key-kbd command)
  (assert (commandp command))
  (assert (stringp key-kbd))
  (define-key sf:minor-mode-map (sf:key-with-prefix key-kbd) command))

;;;; Action Minor Mode
;; Prefix: sf-action:
(defvar sf:action-minor-mode-map
  (make-sparse-keymap))

(define-minor-mode symfony-action-minor-mode
  "Symfony Action Minor Mode"
  nil
  " sfAction"
  sf:action-minor-mode-map
  ;; body
  (setq sf:primary-switch-fn 'sf-action:switch-to-template)
  )

(defun sf-action:switch-to-template ()
  (interactive)
  (let ((templates (sf-action:get-templates)))
    (cond
     (templates
      (sf:anything-project templates))
     (t
      (call-interactively 'sf-cmd:all-project-files)))))

(defun sf:take-class-name ()
  (save-excursion
    (goto-char (point-min))
    (let ((class-name-re (rx bol
                          (* space)
                          "class"
                          (+ space)
                          (group
                           (+
                            (or (syntax word) (syntax symbol))))
                          (or space eol))))
        (when (re-search-forward class-name-re nil t)
          (match-string-no-properties 1)))))

(defun sf-action:get-templates ()
  (cond
   ;; actions/actions.class.php
   ((string= (sf:this-file-name) sf:ACTIONS-CLASS-PHP)
    (let ((action-name (sf:take-off-execute (sf:take-function-name))))
      (sf-action:get-templates-by-action-name action-name)))
   ;; fooAction.class.php case
   (t
    (let ((action-name (sf:take-off-action (sf:take-class-name))))
      (sf-action:get-templates-by-action-name action-name)
      ))))

(defun sf-action:get-templates-by-action-name (action-name)
  (when action-name
    (loop for dir in (sf:get-templates-directory)
          nconc (sf:directory-files-recursively
                 (rx-to-string `(and ,action-name "Success.php"))
                 dir))))

;;;; Template Minor Mode
;; Prefix: sf-template:
(defvar sf:template-minor-mode-map
  (make-sparse-keymap))
(define-minor-mode symfony-template-minor-mode
  "Symfony Template Minor Mode"
  nil
  " sfTemplate"
  sf:template-minor-mode-map
  ;; body
  (setq sf:primary-switch-fn 'sf-template:switch-to-action)
  )

(defun sf-template:switch-to-action ()
  (interactive)
  (lexical-let* ((file-name (file-name-sans-extension (sf:this-file-name)))
                 (action-name (sf:take-off-tail-capital file-name)))
    (when action-name
      (lexical-let* ((actions (sf-template:get-specify-actions-by-action-name action-name))
                     (execute-re (rx-to-string
                                  `(and "public"
                                        (+ space)
                                        "function"
                                        (+ space)
                                        "execute"
                                        ,action-name)))
                     (class-re (rx-to-string `(and "class" (+ space) ,action-name "Action"))))
        (let ((sf:after-anything-project-action-hook
               (list
                (lambda ()
                  (goto-char (point-min))
                  (or (re-search-forward execute-re nil t)
                      (re-search-forward class-re nil t))))))
          (sf:anything-project actions))))))

(defun sf-template:get-specify-actions (action-name)
  "return list of string(file name)"
  (sf-template:get-specify-actions-by-action-name action-name))

;; voteSuccess.php -> user/actions/actions.class.php :: executeVote
;; or
;; voteSuccess.php -> user/actions/actions/voteAction.class.php
(defun sf-template:get-specify-actions-by-action-name (action-name)
  (let* ((module-directory (sf:get-module-directory))
         (actions-directory (sf:catdir module-directory "actions")))
    (assert (and actions-directory
                 (file-directory-p actions-directory)))
    (append (sf-template:get-specify-actions-actions-class action-name actions-directory)
            (sf-template:get-specify-actions-saparate-file action-name actions-directory)
            )))

(defun sf-template:get-specify-actions-actions-class (action-name actions-directory)
  "return list"
  (let ((file-path (sf:catdir actions-directory sf:ACTIONS-CLASS-PHP)))
    (when (and file-path (file-exists-p file-path) (file-readable-p file-path))
      (list file-path))))

(defun sf-template:get-specify-actions-saparate-file (action-name actions-directory)
  "return list"
  (let* ((file-name (concat action-name sf:ACTIONS-FILE-RULE))
         (file-path (sf:catdir actions-directory file-name)))
    (when (and file-path (file-exists-p file-path) (file-readable-p file-path))
      (list file-path))))

;;;; Script
;; Prefix: sf-script:

(defvar sf-script:buffer-name "*Symfony Output*")
(defvar sf-script:history nil)

(defcustom sf-script:symfony-command nil
  "Symfony command(full path).
IF this variable is nil, \"symfony\" command is searched in PATH")

(defun sf-script:symfony-command ()
  (cond
   ((and sf-script:symfony-command
         (file-executable-p sf-script:symfony-command))
    sf-script:symfony-command)
   (t
    (let ((command (executable-find "symfony")))
      (or command
          (error "symfony command is not in PATH"))))))

(defcustom sf-script:coding-system nil
  "this variable is bound to `coding-system-for-read' and `coding-system-for-write'
in `sf-script:start-process'.
IF nil, do nothing")

(defun sf-script:process-running-p ()
  (get-buffer-process sf-script:buffer-name))

(defun sf-script:kill-process ()
  (interactive)
  (let ((proc (sf-script:process-running-p)))
    (when proc
      (prog1 t
        (delete-process proc)
        (message "deleted process")))))

(defun sf-script:process-sentinel (proc message)
  (when (memq (process-status proc) '(exit signal))
    (let* ((status-msg (if (zerop (process-exit-status proc)) "successful" "failure"))
           (msg (format "%s was stopped (%s)."
                       (process-name proc)
                       status-msg)))
      (message (replace-regexp-in-string "\n" "" msg)))))

(defun sf-script:start-process (name buffer-name program &rest args)
  (let ((coding-system-for-read sf-script:coding-system)
        (coding-system-for-write sf-script:coding-system))
    (apply 'start-process-shell-command
           name
           buffer-name
           program
           args)))

(defun sf-script:initialize-output-mode ()
  (set (make-local-variable 'font-lock-keywords-only) t)
  (make-local-variable 'font-lock-defaults)
  (set (make-local-variable 'scroll-margin) 0)
  (set (make-local-variable 'scroll-preserve-screen-position) nil)
  (make-local-variable 'after-change-functions)
  (symfony-minor-mode t))

(defvar sf-script:output-mode-hook nil)
(define-derived-mode sf-script:output-mode fundamental-mode "sfOutput"
  "Major mode to symfony Script Output."
  (sf-script:initialize-output-mode)
  (buffer-disable-undo)
  (setq buffer-read-only t)
  (run-hooks 'sf-script:output-mode-hook))

(defun sf-script:setup-output-buffer (&optional major-mode)
  (with-current-buffer (get-buffer sf-script:buffer-name)
    (let ((buffer-read-only nil))
      (if (and major-mode (functionp major-mode))
          (apply major-mode (list))
        (sf-script:output-mode)))))

(defun sf-script:run (command &optional args major-mode)
  (assert (stringp command))
  (assert (listp args))
  (sf:with-root-default-directory
   (save-some-buffers)
   (cond
    ((sf-script:process-running-p)
     (message "symfony process already running"))
    (t
     (let ((proc (apply 'sf-script:start-process
                        sf-script:buffer-name;(mapconcat 'identity (cons command args) " ")
                        sf-script:buffer-name
                        command
                        args)))
       (sf-script:setup-output-buffer major-mode)
       (set-process-sentinel proc 'sf-script:process-sentinel)
       ;; return proc
       proc
       )))))

(defvar sf-script:command-list
  '("h" "cc""clear-cache" "init-app" "init-module" "init-project" "log-purge" "log-rotate" "plugin-install"
    "plugin-list" "plugin-uninstall" "plugin-upgrade" "clear-controllers" "sync" "disable" "enable" "freeze"
    "permissions, fix-perms" "unfreeze"  "propel-build-all" "propel-build-all-load" "propel-build-model" "propel-build-schema"
    "propel-build-sql" "propel-dump-data" "propel-load-data" "propel-generate-crud" "propel-init-admin" "propel-insert-sql"
    "propel-convert-yml-schema" "propel-convert-xml-schema" "test-all" "test-functional" "test-unit"))

;;; clear-cache (cc)
(defvar sf-script:clear-cache-arg-candidates
  '("template" "config" "i18n"))


;;;; Keybinds
(sf:define-key "C-p" 'sf-cmd:primary-switch)
(sf:define-key "<up>" 'sf-cmd:primary-switch)

(sf:define-key "C-n" 'sf-cmd:relative-files)
(sf:define-key "<down>" 'sf-cmd:relative-files)

(sf:define-key "C-c g m" 'sf-cmd:model-files)
(sf:define-key "C-c g a" 'sf-cmd:action-files)
(sf:define-key "C-c g h" 'sf-cmd:helper-files)
(sf:define-key "C-c g t" 'sf-cmd:template-files)
(sf:define-key "C-c g T" 'sf-cmd:test-files)

(sf:define-key "C-c l" 'sf-cmd:open-log-file)
(sf:define-key "C-c C-t" 'sf-cmd:create-or-update-tags)


;;;; Install
(defun sf:find-file-hook ()
  (symfony-minor-mode-maybe))
;;; add hook to `find-file-hooks'
(add-hook  'find-file-hooks 'sf:find-file-hook)


;;;; Test
(defmacro* sf:with-file-buffer (file &body body)
  (declare (indent 1))
  `(with-current-buffer (find-file-noselect ,file)
     (prog1 (progn ,@body)
       (kill-buffer (current-buffer)))))

(defun sf:directory-separator ()
  (substring (file-name-as-directory ".") -1))

(defun sf:path-to (path &rest paths)
  (assert (or (null paths)
              (and (listp paths)
                   (stringp (car-safe paths)))))
  (assert (stringp path))
  (let ((paths (append (list path) paths)))
    (concat (file-name-directory (locate-library "symfony"))
            (mapconcat 'identity paths (sf:directory-separator)))))

(defmacro sf:with-php-buffer (s &rest body)
  (declare (indent 1))
  `(with-temp-buffer
     (php-mode)
     (insert ,s)
     (goto-char (point-min))
     (when (re-search-forward (rx "`!!'") nil t)
       (replace-match ""))
     (progn
       ,@body)))

(eval-when-compile
  (def-edebug-spec sf:with-php-buffer (stringp &rest form)))

(defmacro sf:with-current-dir (dir &rest body)
  (declare (indent 1))
  `(flet ((sf:current-directory () (file-name-directory ,dir)))
     (progn ,@body)))

(defun sf:askeet-path-to (&rest paths)
  (apply 'sf:path-to "t" "askeet" paths))

(defun sf:to-bool (obj)
  (not (not obj)))

(dont-compile
  (when (fboundp 'expectations)
    (expectations
      (desc "initialize")
      (expect t
        (setq sf:project-cache nil)
        t)

      (desc "case-fold-search")
      (expect t
        (let ((case-fold-search t))
          (sf:to-bool (string-match "^[A-Z]$" "a"))))
      (expect nil
        (let ((case-fold-search nil))
          (string-match "^[A-Z]$" "a")))

      (desc "sf:path-to")
      (expect "t"
        (file-relative-name (sf:path-to "t")))
      (expect "t/askeet/apps"
        (file-relative-name (sf:path-to "t" "askeet" "apps")))

      (desc "sf:with-current-dir")
      (expect "/hoge/"
        (sf:with-current-dir "/hoge/huga" (sf:current-directory)))

      (desc "sf:askeet-path-to")
      (expect "t/askeet/apps/frontend/modules"
        (file-relative-name (sf:askeet-path-to  "apps" "frontend" "modules")))

      (desc "sf:get-project-root")
      (expect "t/askeet/"
        (file-relative-name 
         (sf:with-current-dir (sf:askeet-path-to  "apps" "frontend" "modules")
           (sf:get-project-root))))

      (desc "sf:take-function-name")
      (expect "executeVote"
        (sf:with-php-buffer "
  public function executeVote()
  {
    $this->answer = AnswerPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->answer);
`!!'
    $user = $this->getUser()->getSubscriber();

    $relevancy = new Relevancy();
    $relevancy->setAnswer($this->answer);
    $relevancy->setUser($user);
    $relevancy->setScore($this->getRequestParameter('score') == 1 ? 1 : -1);
    $relevancy->save();
  }
"
          (sf:take-function-name)))

      (desc "sf:take-off-execute")
      (expect "Vote"
        (sf:take-off-execute "executeVote"))
      
      (expect t
        (stringp (sf:take-off-execute "non-match")))
      
      (expect t
        (stringp (sf:take-off-execute "")))

      (desc "sf:catdir")
      (expect "hoge/huga"
        (sf:catdir "hoge/" "/huga" ))

      (desc "sf:get-templates-directory")
      (expect '("t/askeet/apps/frontend/modules/user/templates")
        (mapcar 'file-relative-name
                (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
                  (sf:get-templates-directory))))

      (desc "sf:get-templates-file-by-action-name")
      (expect '("t/askeet/apps/frontend/modules/user/templates")
        (mapcar 'file-relative-name
                (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
                  (sf:get-templates-file-by-action-name "Vote"))))

      (desc "install minor mode")
      (expect t
        (file-exists-p (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")))
      (expect t
        (with-current-buffer (find-file-noselect (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php"))
          (prog1 symfony-minor-mode
            (kill-buffer (current-buffer)))))

      (desc "sf:project-files")
      (expect (sort '("t/askeet/apps/frontend/modules/user" "t/askeet/apps/frontend/modules/tag" "t/askeet/apps/frontend/modules/sidebar" "t/askeet/apps/frontend/modules/question" "t/askeet/apps/frontend/modules/moderator" "t/askeet/apps/frontend/modules/mail" "t/askeet/apps/frontend/modules/feed" "t/askeet/apps/frontend/modules/content" "t/askeet/apps/frontend/modules/api" "t/askeet/apps/frontend/modules/answer" "t/askeet/apps/frontend/modules/administrator") #'string<)
        (sort (sf:abs->relative
         (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
           (remove-if-not (lambda (file-name)
                            (string-match (rx "/modules/" (+ (not (any "/"))) eol) file-name))
                          (sf:project-files t)))) #'string<))

      (desc "sf:get-module-dir-or-root")
      (expect "t/askeet/apps/frontend/modules/"
        (file-relative-name
         (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
           (sf:get-module-dir-or-root))))

      (expect "t/askeet/"
        (file-relative-name
         (sf:with-current-dir (sf:askeet-path-to "apps/frontend/")
           (sf:get-module-dir-or-root))))


      (desc "sf:buffer-type")
      (expect 'action
        (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
          (sf:buffer-type)))

      (expect 'template
        (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/templates/voteSuccess.php")
          (sf:buffer-type)))

      (expect nil
        (sf:with-current-dir (sf:askeet-path-to "apps/")
          (sf:buffer-type)))

      (desc "sf:get-specify-minor-mode-string")
      (expect "symfony-action-minor-mode"
        (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
          (sf:get-specify-minor-mode-string)))

      (desc "sf:specify-minor-mode-maybe")
      (expect t
        (with-current-buffer (find-file-noselect (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php"))
          (prog1 symfony-action-minor-mode
            (ignore-errors (kill-buffer (current-buffer))))))

      ;; Test Action
      (desc "sf-action:get-templates")
      (expect '("t/askeet/apps/frontend/modules/user/templates/listInterestedBySuccess.php")
        (sf:abs->relative
         (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
           (goto-char (point-min))
           (re-search-forward (rx "public function executeListInterestedBy"))
           (forward-line 3)
           (sf-action:get-templates))))
      (expect '("t/askeet/apps/frontend/modules/user/templates/voteSuccess.php")
        (sf:abs->relative
         (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/actions" "voteAction.class.php")
           (re-search-forward (rx "public function execute") nil t)
           (sf-action:get-templates))))

      (desc "sf:get-module-directory")
      (expect "t/askeet/apps/frontend/modules/user/"
        (file-relative-name
         (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/templates/voteSuccess.php")
           (sf:get-module-directory))))

      (desc "sf:take-off-tail-capital")
      (expect "passwordRequest"
        (sf:take-off-tail-capital "passwordRequestSuccess"))
      (expect "vote"
        (sf:take-off-tail-capital "voteSuccess"))
      (expect nil
        (sf:take-off-tail-capital "lowercase"))

      (desc "sf-template:get-specify-actions-by-action-name")
      (expect '("t/askeet/apps/frontend/modules/user/actions/actions.class.php" "t/askeet/apps/frontend/modules/user/actions/voteAction.class.php")
        (sf:abs->relative
         (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/templates/voteSuccess.php")
           (sf-template:get-specify-actions-by-action-name "vote"))))
      (desc "sf-template:switch-to-action")
      (expect t
        (sf:to-bool
         (string-match (rx "public function executeInterested()")
                       (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/templates/interestedSuccess.php")
                         (call-interactively 'sf-template:switch-to-action)
                         (buffer-substring-no-properties (point-at-bol) (point-at-eol))))))

      (desc "sf:relative-files")
      (expect (sort '("t/askeet/apps/frontend/modules/user/validate" "t/askeet/apps/frontend/modules/user/validate/update.yml" "t/askeet/apps/frontend/modules/user/validate/passwordRequest.yml" "t/askeet/apps/frontend/modules/user/validate/login.yml" "t/askeet/apps/frontend/modules/user/validate/add.yml" "t/askeet/apps/frontend/modules/user/templates" "t/askeet/apps/frontend/modules/user/templates/voteSuccess.php" "t/askeet/apps/frontend/modules/user/templates/showSuccess.php" "t/askeet/apps/frontend/modules/user/templates/reportQuestionSuccess.php" "t/askeet/apps/frontend/modules/user/templates/reportAnswerSuccess.php" "t/askeet/apps/frontend/modules/user/templates/passwordRequestSuccess.php" "t/askeet/apps/frontend/modules/user/templates/passwordRequestMailSent.php" "t/askeet/apps/frontend/modules/user/templates/loginSuccess.php" "t/askeet/apps/frontend/modules/user/templates/listInterestedBySuccess.php" "t/askeet/apps/frontend/modules/user/templates/interestedSuccess.php"  "t/askeet/apps/frontend/modules/user/lib" "t/askeet/apps/frontend/modules/user/config" "t/askeet/apps/frontend/modules/user/config/view.yml" "t/askeet/apps/frontend/modules/user/config/security.yml" "t/askeet/apps/frontend/modules/user/actions" "t/askeet/apps/frontend/modules/user/actions/voteAction.class.php" "t/askeet/apps/frontend/modules/user/actions/actions.class.php") #'string<)
        (sort
         (sf:abs->relative
           (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/actions" "voteAction.class.php")
                 (sf:relative-files))) #'string<))

      (desc "sf:get-log-directory")
      (expect "t/askeet/log/"
        (file-relative-name
         (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/actions" "voteAction.class.php")
           (sf:get-log-directory))))

      (desc "sf:project-absolute-path")
      (expect "t/askeet/apps/frontend/"
        (file-relative-name
         (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/actions" "voteAction.class.php")
           (sf:project-absolute-path "apps/frontend/")
           )))

      (desc "sf:with-root-default-directory")
      (expect "t/askeet/"
        (file-relative-name
         (let ((default-directory nil))
           (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/actions" "voteAction.class.php")
             (sf:with-root-default-directory
              default-directory)))))

      (desc "---- sf-script ----")

      (expect t
        (processp
         (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/actions" "voteAction.class.php")
           (let ((perl-bin (executable-find "perl")))
             ;; skip tests unless find perl bin.
             (when perl-bin
               (start-process-shell-command sf-script:buffer-name
                                            sf-script:buffer-name
                                            perl-bin
                                            "-e"
                                            "'sleep(999999); print \"process end\";'"
                                            ))))))
      (expect t
        (processp
         (sf-script:process-running-p)))

      (expect t
        (sf:to-bool
         (sf-script:kill-process)))

      (expect nil
        (sf-script:process-running-p))

      (desc "---- sf-script clean up ----")
      (expect nil
        (while (sf-script:process-running-p)
          (sf-script:kill-process)))

      (desc "sf:get-application-names")
      (expect t
        (sf:to-bool
         (member "frontend"
                 (sf:with-file-buffer (sf:askeet-path-to "apps/frontend/modules/user/actions")
                   (sf:get-application-names)))))

      (desc "sf:remove-if-not-match")
      (expect '("apple")
        (sf:remove-if-not-match (rx bol "apple" eol) '("banana" "qiwi" "apple" "xxapplexx")))

      (desc "sf:project-files using sf:project-cache as ap:--cache")
      (desc "")
      (expect nil
        (let* ((ap:--cache '(("/path/to" "/file1" "/file2")))
               (tmp-ap:--cache (copy-alist ap:--cache)))
          (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
            (with-stub 
              (stub ap:cache-get-or-set => ap:--cache)
              (eq tmp-ap:--cache (sf:project-files))))))
      (expect t
        (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
          (with-stub 
            (stub ap:cache-get-or-set => ap:--cache)
            (eq sf:project-cache (sf:project-files)))))

      (expect t
        (save-window-excursion
          (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
            (with-stub
              (stub yes-or-no-p => t)
              (and (sf-cmd:create-or-update-tags)
                   (prog1 (file-exists-p (sf:project-absolute-path sf:tags-file-name))
                     (delete-file (sf:project-absolute-path sf:tags-file-name))
                     ))))))

      (expect t
        (sf:to-bool
         (string-match
          (rx "ctags -e -a  -R --php-types=c+f+d+v+i -o TAGS --langmap=PHP:.php.inc " (* not-newline) "askeet/apps" (* not-newline) "askeet/lib")
          (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
            (let ((sf:tag-file-name "TAGS")
                  (sf:tags-dirs '("apps" "lib"))
                  (sf:tags-command "ctags -e -a  -R --php-types=c+f+d+v+i -o %s --langmap=PHP:.php.inc -f  %s"))
              (sf:make-create-tags-command)))

          )))

      (desc "php-completion")
      (desc "tags parser")
      (expect t
        (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
          (let ((tag (sf-cmd:create-or-update-tags)))
            (when (and tag (file-exists-p tag))
              (every 'phpcmp-tag-p (phpcmp-etags-get-tags tag))))))

      (desc "sf:get-tags-structs")
      (expect t
        (ignore-errors (delete-file (sf:project-absolute-path sf:tags-file-name)))
        (let ((tags (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
                      (sf-cmd:create-or-update-tags)
                      (sf:get-tags-structs))))
          (and (> (length tags) 10)
               (every 'phpcmp-tag-p (phpcmp-etags-get-tags tags)))))

      (expect t
        (let ((sf:tags-cache nil))
          (let ((tags (sf:with-current-dir (sf:askeet-path-to "apps/frontend/modules/user/actions" "actions.class.php")
                        (sf:get-tags-structs))))
            (and (> (length tags) 10)
                 (every 'phpcmp-tag-p (phpcmp-etags-get-tags tags))))))
      )))

(provide 'symfony)
;; symfony.el ends here.
























































































































